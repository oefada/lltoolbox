<?php
class LdapUsersController extends AppController
{
   var $name = 'LdapUsers';
   var $uses = array('LdapUser');

   function index()
   {
      $users = $this->LdapUser->findAll('samAccountName', '*');

      $this->set('ldap_users', $users);
   }

   function add() {
      if(empty($this->data)) {
         $this->set('ldap_users', null);
         $newuid = $this->LdapUser->findLargestUidNumber() + 1;
         $this->set('newuid',$newuid);
      } else {
         if($this->LdapUser->save($this->data)) {
            if(is_object($this->Session)) {
               $this->Session->setFlash('The LDAP User has been saved');
               $this->redirect('/ldap_users/index');
            } else {
               $this->flash('LDAP User saved.', '/ldap_users/index');
            }
         } else {
            if(is_object($this->Session)) {
               $this->Session->setFlash('Please correct errors below.');
            }
            $data = $this->data;
            $this->set('ldap_users', $data);
         }
      }
   }

   function edit($id) {
      if(empty($this->data)) {
         $data = $this->LdapUser->read(null, $id);

         $this->set('ldap_user', $data );
      } else {
         $this->LdapUser->del($id);
         if($this->LdapUser->save($this->data)) {
            if(is_object($this->Session)) {
               $this->Session->setFlash('The LDAP User has been saved');
               $this->redirect('/ldap_users/index');
            } else {
               $this->flash('LDAP User saved.', '/ldap_users/index');
            }
         } else {
            if(is_object($this->Session)) {
               $this->Session->setFlash('Please correct errors below.');
            }
            $data = $this->data;
            $this->set('ldap_user', $data);
         }
      }
   }

   function view($uid) {
      $this->set('ldap_user', $this->LdapUser->read(null, $uid));
   }

   function delete($id) {
      $this->LdapUser->del($id);
      $this->redirect('/ldap_users/index');
   }
}
?>