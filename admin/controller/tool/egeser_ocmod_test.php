<?php
/**
 * EGESER OCMOD Test Panel + Full Auditor V2.2 Token Safe
 * Author: EGESER
 */
class ControllerToolEgeserOcmodTest extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('tool/egeser_ocmod_test');
        $this->document->setTitle('EGESER OCMOD Test & Auditor');
        $helper = $this->helper();

        $data = $this->baseData();
        $data['modifications'] = $helper->getModifications();
        $data['selected'] = array();
        $data['analysis'] = array();
        $data['start'] = '';
        $data['test_urls'] = $helper->getTestUrls();
        $data['audit_start'] = $this->url->link('tool/egeser_ocmod_test/auditStart', 'token=' . $this->session->data['token'], true);

        if (isset($this->request->get['modification_id'])) {
            $modification_id = (int)$this->request->get['modification_id'];
            $mod = $helper->getModification($modification_id);
            if ($mod) {
                $data['selected'] = $mod;
                $data['analysis'] = $helper->analyzeXml($mod['xml']);
                $data['start'] = $this->url->link('tool/egeser_ocmod_test/start', 'token=' . $this->session->data['token'], true);
            }
        }

        $this->response->setOutput($this->load->view('tool/egeser_ocmod_test', $data));
    }

    /* ---------- Existing single A/B test ---------- */
    public function start() {
        $this->load->language('tool/egeser_ocmod_test');
        if (!$this->validate()) return $this->index();
        if ($this->request->server['REQUEST_METHOD'] !== 'POST' || empty($this->request->post['modification_id'])) {
            $this->session->data['error'] = 'Geçersiz test isteği.';
            $this->response->redirect($this->url->link('tool/egeser_ocmod_test', 'token=' . $this->session->data['token'], true));
            return;
        }
        $helper = $this->helper();
        $modification_id = (int)$this->request->post['modification_id'];
        $mod = $helper->getModification($modification_id);
        if (!$mod) {
            $this->session->data['error'] = 'Modifikasyon bulunamadı.';
            $this->response->redirect($this->url->link('tool/egeser_ocmod_test', 'token=' . $this->session->data['token'], true));
            return;
        }
        $analysis = $helper->analyzeXml($mod['xml']);
        if (!$analysis['safe_ab']) {
            $this->session->data['error'] = 'Bu modifikasyon tekli otomatik A/B test için güvenli değil. Tam Denetim statik/log analizi uygulayabilir.';
            $this->response->redirect($this->url->link('tool/egeser_ocmod_test', 'token=' . $this->session->data['token'] . '&modification_id=' . $modification_id, true));
            return;
        }
        $test_id = $helper->newTestId();
        $urls = $helper->getTestUrls();
        $state = array(
            'test_id' => $test_id, 'stage' => 'baseline_complete', 'started_at' => date('c'),
            'modification_id' => $modification_id, 'modification_name' => $mod['name'], 'modification_code' => $mod['code'],
            'original_status' => (int)$mod['status'], 'toggled_status' => (int)$mod['status'] ? 0 : 1,
            'analysis' => $analysis, 'urls' => $urls, 'baseline' => $helper->snapshotAll($urls)
        );
        $helper->saveState($test_id, $state);
        $helper->setStatus($modification_id, $state['toggled_status']);
        $state['stage'] = 'toggled_waiting_refresh'; $helper->saveState($test_id, $state);
        $this->response->redirect($this->url->link('extension/modification/refresh', 'token=' . $this->session->data['token'] . '&egeser_test_id=' . $test_id, true));
    }

    public function afterToggle() {
        $this->load->language('tool/egeser_ocmod_test'); if (!$this->validate()) return $this->index();
        $helper = $this->helper(); $test_id = $this->cleanId(isset($this->request->get['test_id']) ? $this->request->get['test_id'] : '');
        $state = $helper->loadState($test_id); if (!$state) return $this->goPanel('Test durumu bulunamadı.');
        $state['test'] = $helper->snapshotAll($state['urls']);
        $state['comparison'] = $helper->compareAll($state['baseline'], $state['test']);
        $state['stage'] = 'comparison_complete_restoring'; $helper->saveState($test_id, $state);
        $helper->setStatus($state['modification_id'], $state['original_status']);
        $state['stage'] = 'restore_waiting_refresh'; $helper->saveState($test_id, $state);
        $this->response->redirect($this->url->link('extension/modification/refresh', 'token=' . $this->session->data['token'] . '&egeser_restore_id=' . $test_id, true));
    }

    public function result() {
        $this->load->language('tool/egeser_ocmod_test'); if (!$this->validate()) return $this->index();
        $helper = $this->helper(); $test_id = $this->cleanId(isset($this->request->get['test_id']) ? $this->request->get['test_id'] : '');
        $state = $helper->loadState($test_id); if (!$state) return $this->goPanel('Test sonucu bulunamadı.');
        $current = $helper->getModification($state['modification_id']);
        $state['restored'] = $current && ((int)$current['status'] === (int)$state['original_status']);
        $state['stage'] = $state['restored'] ? 'complete' : 'restore_warning'; $helper->saveState($test_id, $state);
        $data = $this->baseData(); $data['result_state'] = $state;
        $data['back'] = $this->url->link('tool/egeser_ocmod_test', 'token=' . $this->session->data['token'], true);
        $data['restore'] = $this->url->link('tool/egeser_ocmod_test/restore', 'token=' . $this->session->data['token'] . '&test_id=' . $test_id, true);
        $this->response->setOutput($this->load->view('tool/egeser_ocmod_test_result', $data));
    }

    public function restore() {
        $this->load->language('tool/egeser_ocmod_test'); if (!$this->validate()) return $this->index();
        $helper = $this->helper(); $test_id = $this->cleanId(isset($this->request->get['test_id']) ? $this->request->get['test_id'] : '');
        $state = $helper->loadState($test_id);
        if ($state) {
            $helper->setStatus($state['modification_id'], $state['original_status']);
            $this->response->redirect($this->url->link('extension/modification/refresh', 'token=' . $this->session->data['token'] . '&egeser_restore_id=' . $test_id, true)); return;
        }
        $this->response->redirect($this->url->link('tool/egeser_ocmod_test', 'token=' . $this->session->data['token'], true));
    }

    /* ---------- Full audit ---------- */
    public function auditStart() {
        $this->load->language('tool/egeser_ocmod_test');
        if (!$this->validate()) return $this->index();
        if ($this->request->server['REQUEST_METHOD'] !== 'POST') return $this->goPanel('Tam denetim POST ile başlatılmalıdır.');
        $helper = $this->helper();
        $auto = !empty($this->request->post['auto_disable']);
        $state = $helper->createAuditState($auto);
        $audit_id = $state['audit_id'];
        $state['stage'] = 'initial_refresh'; $helper->saveAuditState($audit_id, $state);
        $this->response->redirect($this->url->link('extension/modification/refresh', 'token=' . $this->session->data['token'] . '&egeser_audit_init_id=' . $audit_id, true));
    }

    public function auditAfterInitial() {
        if (!$this->validate()) return $this->index();
        $helper = $this->helper(); $audit_id = $this->cleanId(isset($this->request->get['audit_id']) ? $this->request->get['audit_id'] : '');
        $state = $helper->loadAuditState($audit_id); if (!$state) return $this->goPanel('Tam denetim durumu bulunamadı.');
        $state['initial_health'] = $helper->snapshotAll($state['urls']);
        $state['stage'] = 'running'; $helper->saveAuditState($audit_id, $state);
        $this->renderAuditProgress($state, $this->url->link('tool/egeser_ocmod_test/auditStep', 'token=' . $this->session->data['token'] . '&audit_id=' . $audit_id, true), 'Başlangıç ölçümü tamamlandı.');
    }

    public function auditStep() {
        if (!$this->validate()) return $this->index();
        $helper = $this->helper(); $audit_id = $this->cleanId(isset($this->request->get['audit_id']) ? $this->request->get['audit_id'] : '');
        $state = $helper->loadAuditState($audit_id); if (!$state) return $this->goPanel('Tam denetim durumu bulunamadı.');

        if ((int)$state['index'] >= count($state['queue'])) {
            $state['stage'] = 'complete'; $state['completed_at'] = date('c');
            $state['final_health'] = $helper->snapshotAll($state['urls']);
            $helper->saveAuditState($audit_id, $state);
            $this->response->redirect($this->url->link('tool/egeser_ocmod_test/auditResult', 'token=' . $this->session->data['token'] . '&audit_id=' . $audit_id, true)); return;
        }

        $id = (int)$state['queue'][(int)$state['index']];
        $mod = $helper->getModification($id);
        if (!$mod || !(int)$mod['status']) {
            $state['results'][$id] = array('modification_id'=>$id, 'name'=>isset($state['static'][$id]['name'])?$state['static'][$id]['name']:'Bilinmeyen', 'decision'=>'already_disabled', 'reason'=>'Denetim sırasında zaten kapalıydı.');
            $state['index']++; $helper->saveAuditState($audit_id, $state);
            return $this->renderAuditProgress($state, $this->url->link('tool/egeser_ocmod_test/auditStep', 'token=' . $this->session->data['token'] . '&audit_id=' . $audit_id, true), 'Kapalı mod atlandı.');
        }

        $analysis = $helper->analyzeXml($mod['xml']);
        $base = array('modification_id'=>$id, 'name'=>$mod['name'], 'code'=>$mod['code'], 'author'=>$mod['author'], 'version'=>$mod['version'], 'analysis'=>$analysis);

        // Broken XML/regex or empty OCMOD can be safely disabled: they do not provide a valid runtime transformation.
        if (!$analysis['valid_xml'] || !$analysis['regex_valid'] || (int)$analysis['operations'] === 0) {
            $reason = !$analysis['valid_xml'] ? 'Geçersiz XML' : (!$analysis['regex_valid'] ? 'Geçersiz regex' : 'OCMOD operasyonu yok');
            if (!empty($state['auto_disable'])) {
                $helper->setStatus($id, 0);
                $base['decision'] = !$analysis['valid_xml'] || !$analysis['regex_valid'] ? 'broken_disabled' : 'empty_disabled';
                $base['reason'] = $reason . ' — otomatik kapatıldı.';
                $state['disabled_ids'][] = $id; $state['results'][$id] = $base; $state['pending_disabled_id'] = $id;
                $helper->saveAuditState($audit_id, $state);
                $this->response->redirect($this->url->link('extension/modification/refresh', 'token=' . $this->session->data['token'] . '&egeser_audit_disabled_id=' . $audit_id, true)); return;
            }
            $base['decision'] = 'broken_unknown'; $base['reason'] = $reason . ' — otomatik kapatma kapalı.';
            $state['results'][$id] = $base; $state['index']++; $helper->saveAuditState($audit_id, $state);
            return $this->renderAuditProgress($state, $this->url->link('tool/egeser_ocmod_test/auditStep', 'token=' . $this->session->data['token'] . '&audit_id=' . $audit_id, true), $reason);
        }

        // Admin/system hedefleri aktif admin oturumunu, token/izin akışını veya çekirdek runtime'ı etkileyebilir.
        // Bu nedenle Tam Denetim bunların status değerini ASLA değiştirmez. Yalnızca statik + OCMOD log analizi raporlanır.
        if (!empty($analysis['critical_targets'])) {
            $log = $helper->getLatestOcmodLogSection($mod['name']);
            $base['log_effect'] = $log;
            $base['decision'] = 'protected_review_only';
            if ((int)$log['applied_lines'] > 0) {
                $base['reason'] = 'Admin/system hedefi var ve OCMOD logunda uygulanan satır bulundu. Oturum güvenliği için açık bırakıldı; otomatik toggle/disable yapılmadı.';
            } else {
                $base['reason'] = 'Admin/system hedefi var. Son logda uygulanmış satır görülmedi ancak oturum güvenliği için otomatik kapatılmadı; manuel inceleme adayı.';
            }
            $state['kept_ids'][] = $id; $state['results'][$id] = $base; $state['index']++; $helper->saveAuditState($audit_id, $state);
            return $this->renderAuditProgress($state, $this->url->link('tool/egeser_ocmod_test/auditStep', 'token=' . $this->session->data['token'] . '&audit_id=' . $audit_id, true), 'Admin/system hedefli mod korumalı analiz edildi; status değiştirilmedi.');
        }

        // Runtime A/B: full modification-cache tree is authoritative; frontend fingerprints explain user-facing differences.
        $state['current_test'] = array(
            'id'=>$id, 'mod'=>$base,
            'before_tree'=>$helper->fingerprintModificationTree(),
            'before_pages'=>$helper->snapshotAll($state['urls'])
        );
        $helper->setStatus($id, 0);
        $state['stage'] = 'toggle_refresh'; $helper->saveAuditState($audit_id, $state);
        $this->response->redirect($this->url->link('extension/modification/refresh', 'token=' . $this->session->data['token'] . '&egeser_audit_toggle_id=' . $audit_id, true));
    }

    public function auditAfterToggle() {
        if (!$this->validate()) return $this->index();
        $helper = $this->helper(); $audit_id = $this->cleanId(isset($this->request->get['audit_id']) ? $this->request->get['audit_id'] : '');
        $state = $helper->loadAuditState($audit_id); if (!$state || empty($state['current_test'])) return $this->goPanel('A/B denetim durumu eksik.');
        $t = $state['current_test']; $id = (int)$t['id']; $base = $t['mod'];
        $afterTree = $helper->fingerprintModificationTree();
        $afterPages = $helper->snapshotAll($state['urls']);
        $treeDiff = $helper->compareModificationTrees($t['before_tree'], $afterTree);
        $pageDiff = $helper->compareAll($t['before_pages'], $afterPages);
        $pageSummary = $helper->summarizePageDiffs($pageDiff);
        $base['tree_diff'] = $treeDiff; $base['page_summary'] = $pageSummary;

        if (!empty($treeDiff['same'])) {
            // No generated OCMOD file changed when this mod was disabled => provably inert in current build.
            $base['decision'] = !empty($state['auto_disable']) ? 'inert_disabled' : 'inert_found';
            $base['reason'] = 'Kapatıldığında system/storage/modification çıktısında hiçbir dosya değişmedi. Mevcut build için etkisiz/redundant.';
            if (!empty($state['auto_disable'])) $state['disabled_ids'][] = $id;
            else { $helper->setStatus($id, 1); $state['pending_restore_id'] = $id; }
            $state['results'][$id] = $base; unset($state['current_test']);
            if (!empty($state['auto_disable'])) {
                $state['index']++; $helper->saveAuditState($audit_id, $state);
                return $this->renderAuditProgress($state, $this->url->link('tool/egeser_ocmod_test/auditStep', 'token=' . $this->session->data['token'] . '&audit_id=' . $audit_id, true), 'Etkisiz mod kapalı bırakıldı.');
            }
            $helper->saveAuditState($audit_id, $state);
            $this->response->redirect($this->url->link('extension/modification/refresh', 'token=' . $this->session->data['token'] . '&egeser_audit_restore_id=' . $audit_id, true)); return;
        }

        // It changes generated code. Keep it, irrespective of whether sampled pages happen to show a difference.
        $base['decision'] = 'effective_kept';
        $base['reason'] = 'Kapatıldığında OCMOD cache içinde ' . (int)$treeDiff['changed_count'] . ' dosya değişti; çalışma koduna gerçek etkisi var.';
        $state['kept_ids'][] = $id; $state['results'][$id] = $base;
        $helper->setStatus($id, 1); $state['pending_restore_id'] = $id; unset($state['current_test']);
        $helper->saveAuditState($audit_id, $state);
        $this->response->redirect($this->url->link('extension/modification/refresh', 'token=' . $this->session->data['token'] . '&egeser_audit_restore_id=' . $audit_id, true));
    }

    public function auditAfterRestore() {
        if (!$this->validate()) return $this->index();
        $helper = $this->helper(); $audit_id = $this->cleanId(isset($this->request->get['audit_id']) ? $this->request->get['audit_id'] : '');
        $state = $helper->loadAuditState($audit_id); if (!$state) return $this->goPanel('Restore durumu bulunamadı.');
        unset($state['pending_restore_id']); $state['index']++; $state['stage'] = 'running'; $helper->saveAuditState($audit_id, $state);
        $this->renderAuditProgress($state, $this->url->link('tool/egeser_ocmod_test/auditStep', 'token=' . $this->session->data['token'] . '&audit_id=' . $audit_id, true), 'Etkili mod eski durumuna getirildi.');
    }

    public function auditAfterDisabled() {
        if (!$this->validate()) return $this->index();
        $helper = $this->helper(); $audit_id = $this->cleanId(isset($this->request->get['audit_id']) ? $this->request->get['audit_id'] : '');
        $state = $helper->loadAuditState($audit_id); if (!$state) return $this->goPanel('Kapatma durumu bulunamadı.');
        unset($state['pending_disabled_id']); $state['index']++; $state['stage'] = 'running'; $helper->saveAuditState($audit_id, $state);
        $this->renderAuditProgress($state, $this->url->link('tool/egeser_ocmod_test/auditStep', 'token=' . $this->session->data['token'] . '&audit_id=' . $audit_id, true), 'Etkisiz/bozuk mod kapatıldı ve cache yenilendi.');
    }

    public function auditResult() {
        if (!$this->validate()) return $this->index();
        $helper = $this->helper(); $audit_id = $this->cleanId(isset($this->request->get['audit_id']) ? $this->request->get['audit_id'] : '');
        $state = $helper->loadAuditState($audit_id); if (!$state) return $this->goPanel('Denetim sonucu bulunamadı.');
        $data = $this->baseData(); $data['audit'] = $state; $data['counts'] = $helper->auditResultCounts($state);
        $data['rollback'] = $this->url->link('tool/egeser_ocmod_test/auditRollback', 'token=' . $this->session->data['token'] . '&audit_id=' . $audit_id, true);
        $data['back'] = $this->url->link('extension/modification', 'token=' . $this->session->data['token'], true);
        $this->response->setOutput($this->load->view('tool/egeser_ocmod_audit_result', $data));
    }

    public function auditRollback() {
        if (!$this->validate()) return $this->index();
        $helper = $this->helper(); $audit_id = $this->cleanId(isset($this->request->get['audit_id']) ? $this->request->get['audit_id'] : '');
        $state = $helper->loadAuditState($audit_id); if (!$state) return $this->goPanel('Rollback için denetim kaydı bulunamadı.');
        $helper->restoreStatuses($state['original_statuses']); $state['stage'] = 'rollback_refresh'; $helper->saveAuditState($audit_id, $state);
        $this->response->redirect($this->url->link('extension/modification/refresh', 'token=' . $this->session->data['token'] . '&egeser_audit_rollback_id=' . $audit_id, true));
    }

    public function auditAfterRollback() {
        if (!$this->validate()) return $this->index();
        $helper = $this->helper(); $audit_id = $this->cleanId(isset($this->request->get['audit_id']) ? $this->request->get['audit_id'] : '');
        $state = $helper->loadAuditState($audit_id); if (!$state) return $this->goPanel('Rollback sonucu bulunamadı.');
        $state['stage'] = 'rolled_back'; $state['rolled_back_at'] = date('c'); $helper->saveAuditState($audit_id, $state);
        $this->session->data['success'] = 'EGESER OCMOD Auditor: denetim öncesi modifikasyon durumları geri yüklendi.';
        $this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], true));
    }

    /* ---------- EGESER OCMOD Final Cleanup V1.0 ---------- */
    public function cleanup() {
        if (!$this->validate()) return $this->index();
        $helper = $this->helper();
        $audit = $helper->getLatestCompletedAuditState();
        if (!$audit) return $this->goPanel('Final temizlik için tamamlanmış bir EGESER Tam Denetim kaydı bulunamadı.');

        $data = $this->baseData();
        $data['heading_title'] = 'EGESER OCMOD Final Temizlik';
        $data['audit'] = $audit;
        $data['plan'] = $helper->buildCleanupPlan($audit);
        $data['action'] = $this->url->link('tool/egeser_ocmod_test/cleanupExecute', 'token=' . $this->session->data['token'], true);
        $data['back'] = $this->url->link('extension/modification', 'token=' . $this->session->data['token'], true);
        $data['backups'] = $helper->listCleanupBackups();
        foreach ($data['backups'] as &$backup) {
            $backup['rollback_url'] = $this->url->link('tool/egeser_ocmod_test/cleanupRollback', 'token=' . $this->session->data['token'] . '&cleanup_id=' . $backup['cleanup_id'], true);
        }
        unset($backup);
        $this->response->setOutput($this->load->view('tool/egeser_ocmod_cleanup', $data));
    }

    public function cleanupExecute() {
        if (!$this->validate()) return $this->index();
        if ($this->request->server['REQUEST_METHOD'] !== 'POST') return $this->cleanup();
        $helper = $this->helper();
        $audit = $helper->getLatestCompletedAuditState();
        if (!$audit) return $this->goPanel('Temizlik için tamamlanmış denetim bulunamadı.');

        $plan = $helper->buildCleanupPlan($audit);
        $allowed = array();
        foreach ($plan as $row) $allowed[(int)$row['modification_id']] = $row;
        $selected = isset($this->request->post['selected']) ? (array)$this->request->post['selected'] : array();
        $ids = array();
        foreach ($selected as $id) {
            $id = (int)$id;
            if ($id && isset($allowed[$id])) $ids[] = $id;
        }
        $ids = array_values(array_unique($ids));
        if (!$ids) {
            $this->session->data['error'] = 'Silinecek güvenli aday seçilmedi.';
            $this->response->redirect($this->url->link('tool/egeser_ocmod_test/cleanup', 'token=' . $this->session->data['token'], true));
            return;
        }

        // Son anda tekrar dogrula: aktif kayit kesinlikle silinmez.
        $rows = $helper->getRowsByIds($ids);
        $safe_rows = array();
        $safe_ids = array();
        foreach ($rows as $row) {
            $id = (int)$row['modification_id'];
            if ((int)$row['status'] !== 0 || !isset($allowed[$id])) continue;
            $safe_rows[] = $row; $safe_ids[] = $id;
        }
        if (!$safe_ids) return $this->goPanel('Son güvenlik kontrolünde silinebilecek kapalı kayıt kalmadı.');

        $cleanup_id = $helper->newCleanupId();
        if (!$helper->saveCleanupBackup($cleanup_id, isset($audit['audit_id']) ? $audit['audit_id'] : '', $safe_rows)) {
            return $this->goPanel('Temizlik yedeği yazılamadı; hiçbir kayıt silinmedi.');
        }

        $helper->deleteModificationIds($safe_ids);
        $helper->resetOcmodLog();
        $this->response->redirect($this->url->link('extension/modification/refresh', 'token=' . $this->session->data['token'] . '&egeser_cleanup_id=' . $cleanup_id, true));
    }

    public function cleanupResult() {
        if (!$this->validate()) return $this->index();
        $helper = $this->helper();
        $cleanup_id = $this->cleanId(isset($this->request->get['cleanup_id']) ? $this->request->get['cleanup_id'] : '');
        $backup = $helper->loadCleanupBackup($cleanup_id);
        if (!$backup) return $this->goPanel('Temizlik sonucu/yedeği bulunamadı.');

        $remaining = array();
        foreach ($helper->getModifications() as $mod) {
            if ((int)$mod['status'] === 1) $remaining[] = $mod;
        }
        $data = $this->baseData();
        $data['heading_title'] = 'EGESER OCMOD Final Temizlik Sonucu';
        $data['cleanup'] = $backup;
        $data['health'] = $helper->cleanupHealthSummary();
        $data['remaining_active'] = $remaining;
        $data['rollback'] = $this->url->link('tool/egeser_ocmod_test/cleanupRollback', 'token=' . $this->session->data['token'] . '&cleanup_id=' . $cleanup_id, true);
        $data['back'] = $this->url->link('extension/modification', 'token=' . $this->session->data['token'], true);
        $this->response->setOutput($this->load->view('tool/egeser_ocmod_cleanup_result', $data));
    }

    public function cleanupRollback() {
        if (!$this->validate()) return $this->index();
        $helper = $this->helper();
        $cleanup_id = $this->cleanId(isset($this->request->get['cleanup_id']) ? $this->request->get['cleanup_id'] : '');
        $backup = $helper->loadCleanupBackup($cleanup_id);
        if (!$backup || empty($backup['rows'])) return $this->goPanel('Geri alınacak temizlik yedeği bulunamadı.');
        $helper->restoreModificationRows($backup['rows']);
        $helper->markCleanupRestored($cleanup_id);
        $helper->resetOcmodLog();
        $this->response->redirect($this->url->link('extension/modification/refresh', 'token=' . $this->session->data['token'] . '&egeser_cleanup_rollback_id=' . $cleanup_id, true));
    }

    public function cleanupRollbackResult() {
        if (!$this->validate()) return $this->index();
        $cleanup_id = $this->cleanId(isset($this->request->get['cleanup_id']) ? $this->request->get['cleanup_id'] : '');
        $this->session->data['success'] = 'EGESER OCMOD Final Temizlik geri alındı; silinen modifikasyon kayıtları yedekten geri yüklendi ve OCMOD cache yeniden oluşturuldu.';
        $this->response->redirect($this->url->link('extension/modification', 'token=' . $this->session->data['token'], true));
    }

    private function renderAuditProgress($state, $next, $message) {
        $data = $this->baseData();
        // OpenCart 2.3 Url::link() HTML icin &amp; uretir.
        // Bu URL JavaScript window.location ile kullanildiginda entity tarayici tarafinda
        // otomatik cozulmez ve token parametresi 'amp;token' olur.
        // Otomatik denetim adimlarinda gercek URL karakterlerini kullan.
        $next = html_entity_decode($next, ENT_QUOTES, 'UTF-8');
        $data['audit'] = $state; $data['next_url'] = $next; $data['message'] = $message;
        $data['processed'] = (int)$state['index']; $data['total'] = count($state['queue']);
        $data['percent'] = $data['total'] ? min(100, round(($data['processed'] / $data['total']) * 100)) : 100;
        $this->response->setOutput($this->load->view('tool/egeser_ocmod_audit_progress', $data));
    }

    private function helper() { require_once(DIR_SYSTEM . 'library/egeser_ocmod_test.php'); return new EgeserOcmodTest($this->registry); }

    private function baseData() {
        $data = array(); $data['heading_title'] = 'EGESER OCMOD Test & Auditor';
        $data['text_intro'] = 'Tekli A/B test ve toplu OCMOD denetimi. Tam denetim yalnız etkisizliği kanıtlanan aktif modları otomatik kapatır; silme yapmaz.';
        $data['breadcrumbs'] = array(
            array('text'=>'Ana Sayfa','href'=>$this->url->link('common/dashboard','token='.$this->session->data['token'],true)),
            array('text'=>'Modifikasyonlar','href'=>$this->url->link('extension/modification','token='.$this->session->data['token'],true)),
            array('text'=>'EGESER OCMOD Auditor','href'=>$this->url->link('tool/egeser_ocmod_test','token='.$this->session->data['token'],true))
        );
        $data['modification_list'] = $this->url->link('extension/modification','token='.$this->session->data['token'],true);
        $data['token']=$this->session->data['token']; $data['error_warning']=isset($this->session->data['error'])?$this->session->data['error']:''; unset($this->session->data['error']);
        $data['header']=$this->load->controller('common/header'); $data['column_left']=$this->load->controller('common/column_left'); $data['footer']=$this->load->controller('common/footer'); return $data;
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/modification')) { $this->session->data['error']='Modifikasyon değiştirme yetkiniz yok.'; return false; }
        return true;
    }
    private function cleanId($v) { return preg_replace('/[^a-zA-Z0-9_-]/','',(string)$v); }
    private function goPanel($message) { $this->session->data['error']=$message; $this->response->redirect($this->url->link('tool/egeser_ocmod_test','token='.$this->session->data['token'],true)); }
}
