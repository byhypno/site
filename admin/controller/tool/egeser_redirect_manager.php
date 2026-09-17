<?php
class ControllerToolEgeserRedirectManager extends Controller {
    private $error = array();

    public function index() {
        $this->document->setTitle('EGESER 301 Yönlendirme Merkezi');
        $this->load->model('tool/egeser_redirect_manager');
        $data=array('error_warning'=>'','success'=>'','preview'=>array(),'preview_summary'=>array());
        $table_ready = $this->model_tool_egeser_redirect_manager->tableExists();
        if (!$table_ready) {
            $data['error_warning'] = '301 yönlendirme tablosu henüz kurulu değil. Önce EGESER_PAKET_4B_SAFE_DB_APPLY.sql dosyasını uygulayın.';
        }

        if (isset($this->session->data['egeser_redirect_success'])) { $data['success']=$this->session->data['egeser_redirect_success']; unset($this->session->data['egeser_redirect_success']); }

        if ($table_ready && $this->request->server['REQUEST_METHOD']==='POST') {
            $mode=isset($this->request->post['mode'])?$this->request->post['mode']:'';
            if (!$this->user->hasPermission('modify','catalog/product')) {
                $data['error_warning']='Bu işlem için ürün değiştirme yetkisi gerekiyor.';
            } elseif ($mode==='add') {
                $result=$this->model_tool_egeser_redirect_manager->addRule($this->request->post);
                if (!empty($result['errors'])) $data['error_warning']=implode(' ', $result['errors']);
                else { $this->session->data['egeser_redirect_success']='301 kuralı eklendi.'; $this->redirectSelf(); return; }
            } elseif ($mode==='toggle') {
                $toggle=$this->model_tool_egeser_redirect_manager->toggle(isset($this->request->post['redirect_id'])?(int)$this->request->post['redirect_id']:0);
                if (empty($toggle['ok'])) $data['error_warning']=isset($toggle['error'])?$toggle['error']:'Durum değiştirilemedi.';
                else { $this->session->data['egeser_redirect_success']='Kural durumu değiştirildi.'; $this->redirectSelf(); return; }
            } elseif ($mode==='delete') {
                $this->model_tool_egeser_redirect_manager->deleteRule(isset($this->request->post['redirect_id'])?(int)$this->request->post['redirect_id']:0);
                $this->session->data['egeser_redirect_success']='Kural silindi.'; $this->redirectSelf(); return;
            } elseif ($mode==='preview_csv') {
                $pv=$this->previewCsv();
                if (!empty($pv['error'])) $data['error_warning']=$pv['error'];
                else { $data['preview']=$pv['rows']; $data['preview_summary']=$pv['summary']; $this->session->data['egeser_redirect_preview']=$pv['rows']; }
            } elseif ($mode==='apply_csv') {
                if (empty($this->request->post['confirm_apply'])) $data['error_warning']='Uygulama onay kutusunu işaretleyin.';
                elseif (empty($this->session->data['egeser_redirect_preview'])) $data['error_warning']='Önizleme süresi dolmuş. CSV dosyasını yeniden önizleyin.';
                else {
                    $created=0;$updated=0;$skipped=0;
                    foreach ($this->session->data['egeser_redirect_preview'] as $row) {
                        if (!empty($row['errors'])) { $skipped++; continue; }
                        $res=$this->model_tool_egeser_redirect_manager->upsertRule($row);
                        if ($res==='create') $created++; elseif ($res==='update') $updated++; else $skipped++;
                    }
                    unset($this->session->data['egeser_redirect_preview']);
                    $this->session->data['egeser_redirect_success']='CSV uygulandı. Yeni: '.$created.', güncellenen: '.$updated.', atlanan: '.$skipped.'.';
                    $this->redirectSelf(); return;
                }
            }
        }

        $data['rules']=$table_ready ? $this->model_tool_egeser_redirect_manager->getRules() : array();
        $data['token']=$this->session->data['token'];
        $data['action']=$this->url->link('tool/egeser_redirect_manager','token='.$this->session->data['token'],true);
        $data['template_url']=$this->url->link('tool/egeser_redirect_manager/template','token='.$this->session->data['token'],true);
        $data['catalog_base']=defined('HTTP_CATALOG')?rtrim(HTTP_CATALOG,'/'):'';
        $data['breadcrumbs']=array(
            array('text'=>'Ana Sayfa','href'=>$this->url->link('common/dashboard','token='.$this->session->data['token'],true)),
            array('text'=>'301 Yönlendirmeler','href'=>$data['action'])
        );
        $data['header']=$this->load->controller('common/header');
        $data['column_left']=$this->load->controller('common/column_left');
        $data['footer']=$this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('tool/egeser_redirect_manager.tpl',$data));
    }

    private function redirectSelf() { $this->response->redirect($this->url->link('tool/egeser_redirect_manager','token='.$this->session->data['token'],true)); }

    public function template() {
        if (!$this->user->hasPermission('access','catalog/product')) { $this->response->addHeader('HTTP/1.1 403 Forbidden'); return; }
        $out=fopen('php://temp','r+'); fwrite($out,"\xEF\xBB\xBF");
        fputcsv($out,array('source_url','target_url','status','block_disabled','priority','source','note'),';');
        fputcsv($out,array('/eski-url','/yeni-url','0','1','NORMAL','Manuel','Önce Dry Run ile kontrol edin.'),';');
        rewind($out); $csv=stream_get_contents($out); fclose($out);
        $this->response->addHeader('Content-Type: text/csv; charset=UTF-8');
        $this->response->addHeader('Content-Disposition: attachment; filename="Egeser_301_Yonlendirme_Sablonu.csv"');
        $this->response->addHeader('Cache-Control: no-store');
        $this->response->setOutput($csv);
    }

    private function previewCsv() {
        if (!isset($this->request->files['import_file'])) return array('error'=>'CSV dosyası seçilmedi.');
        $err=isset($this->request->files['import_file']['error'])?(int)$this->request->files['import_file']['error']:UPLOAD_ERR_NO_FILE;
        if ($err!==UPLOAD_ERR_OK) return array('error'=>'CSV yüklenemedi. Hata kodu: '.$err);
        $fh=fopen($this->request->files['import_file']['tmp_name'],'r'); if (!$fh) return array('error'=>'CSV açılamadı.');
        $headers=fgetcsv($fh,0,';'); if (!$headers) { fclose($fh); return array('error'=>'CSV başlık satırı okunamadı.'); }
        if (isset($headers[0])) $headers[0]=preg_replace('/^\xEF\xBB\xBF/','',$headers[0]);
        $headers=array_map('trim',$headers); $required=array('source_url','target_url','status','block_disabled','priority','source','note');
        foreach ($required as $r) if (!in_array($r,$headers,true)) { fclose($fh); return array('error'=>'Eksik CSV sütunu: '.$r); }
        $rows=array(); $line=1; $errors=0; $warnings=0; $creates=0; $updates=0;
        while (($vals=fgetcsv($fh,0,';'))!==false) {
            $line++; if ($line>1001) break;
            if (count(array_filter($vals,function($v){return trim((string)$v)!=='';}))===0) continue;
            $row=array(); foreach ($headers as $i=>$key) $row[$key]=isset($vals[$i])?trim($vals[$i]):'';
            $row['status']=in_array(strtolower((string)$row['status']),array('1','true','evet','aktif'),true)?1:0;
            $row['block_disabled']=in_array(strtolower((string)$row['block_disabled']),array('1','true','evet'),true)?1:0;
            $v=$this->model_tool_egeser_redirect_manager->validateRule($row, 0, true);
            $row['source_url']=$v['source_url']; $row['target_url']=$v['target_url']; $row['errors']=$v['errors']; $row['warnings']=array(); $row['line']=$line;
            if (!$row['errors'] && $row['status'] && $this->model_tool_egeser_redirect_manager->checkLoop($row['source_url'],$row['target_url'])) $row['errors'][]='Aktif edilirse yönlendirme döngüsü oluşabilir.';
            $existing=$v['source_hash']?$this->model_tool_egeser_redirect_manager->getRuleByHash($v['source_hash']):false;
            $row['action']=$existing?'update':'create';
            if ($row['errors']) $errors+=count($row['errors']); else { if ($existing) $updates++; else $creates++; }
            if (!$row['status']) { $row['warnings'][]='Kural pasif gelecek.'; $warnings++; }
            $rows[]=$row;
        }
        fclose($fh);

        // Aynı CSV içindeki A->B, B->A gibi çapraz döngüleri Dry Run aşamasında yakala.
        $active_map=array();
        foreach ($rows as $r) if (empty($r['errors']) && !empty($r['status'])) $active_map[$r['source_url']]=$r['target_url'];
        foreach ($rows as $idx=>$r) {
            if (!empty($r['errors']) || empty($r['status'])) continue;
            $seen=array($r['source_url']=>true); $cur=$r['target_url']; $loop=false;
            for ($step=0;$step<50;$step++) {
                if (isset($seen[$cur])) { $loop=true; break; }
                $seen[$cur]=true;
                if (!isset($active_map[$cur])) break;
                $cur=$active_map[$cur];
            }
            if ($loop) { $rows[$idx]['errors'][]='CSV içindeki kurallarla yönlendirme döngüsü oluşur.'; $errors++; }
        }

        return array('rows'=>$rows,'summary'=>array('rows'=>count($rows),'creates'=>$creates,'updates'=>$updates,'errors'=>$errors,'warnings'=>$warnings));
    }
}
