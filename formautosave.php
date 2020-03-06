<?php

require_once 'formautosave.civix.php';
use CRM_Formautosave_ExtensionUtil as E;

/**
 * Implementation of hook_civicrm_config
 */
function formautosave_civicrm_config(&$config) {
  _formautosave_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function formautosave_civicrm_xmlMenu(&$files) {
  _formautosave_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function formautosave_civicrm_install() {
  return _formautosave_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function formautosave_civicrm_uninstall() {
  return _formautosave_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function formautosave_civicrm_enable() {
  return _formautosave_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function formautosave_civicrm_disable() {
  return _formautosave_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function formautosave_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _formautosave_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function formautosave_civicrm_managed(&$entities) {
  return _formautosave_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_buildForm().
 */
function formautosave_civicrm_buildForm($formName, &$form) {
  // Do not load if being called from a snippet (assuming custom group).
  if (CRM_Utils_Request::retrieve('snippet', 'String') == 4) {
    return;
  }

  // Avoid loading the .js twice, which would formautosave init twice
  // with double-binding of events, etc.
  if ($formName == 'CRM_Custom_Form_CustomDataByType') {
    return;
  }

  CRM_Core_Resources::singleton()
    ->addScriptFile('ca.bidon.formautosave', 'formautosave.js')
    ->addStyleFile('ca.bidon.formautosave', 'formautosave.css')
    ->addScriptFile('ca.bidon.formautosave', '/dist/filesaver/FileSaver.min.js');

  // By default the key used is the form name, but this can be very annoying in
  // some circumstances, such as in Case Activities, where we know that the
  // contact_id could be used to avoid restoring info from another case.
  //
  // The JS will default to the form#ID (Ex: Activity), and will append the
  // keysuffix to that ID, so that in a Case Activity, it will be, for example,
  // Activity,12,23, where 12 is the contact_id, and 23 the case_id.
  //
  // The .js determine the form#ID, because a page can have multiple forms.
  //
  // NB: items are saved in the storage with the key:
  // form_id|input_id

  $keysuffix = '';

  if ($formName == 'CRM_Case_Form_Activity') {
    $contact_id = CRM_Utils_Array::value('cid', $_REQUEST);
    $case_id = CRM_Utils_Array::value('caseid', $_REQUEST);

    $keysuffix = "$contact_id,$case_id";
  }

  CRM_Core_Resources::singleton()->addSetting(array(
    'formautosave' => array(
      'keysuffix' => $keysuffix,
    ),
  ));
}

