<?php

     /**
     * Auditable Behavior. (orig: Diffable Behavior)
     *
     * Allows the recording of all changes to a record using inline diff as well as the
     * ability to easily rollback to any previous revision with $model->rollback($Diff_id)
     *
     * Copyright (c), Ben Milleare
	 * Changes and adaptation by Victor Garcia
     *
     * Licensed under The MIT License
     * Redistributions of files must retain the above copyright notice.
     *
     * @filesource
     * @version         0.2
     * @author       Ben Milleare (bgmill / bmilleare), adaptation by Victor Garcia
     * @license      http://www.opensource.org/licenses/mit-license.php The MIT License
     */

     class AuditableBehavior extends ModelBehavior
     {

         function setup(&$model, $config = array())
         {
             $this->settings[$model->name] = am (array('fields' => null),$config);
         }

         function beforeSave(&$model)
         {
             extract($this->settings[$model->name]);

             if (!empty($model->data[$model->name][$model->primaryKey]))
             {
                 App::import('Vendor', 'inline_function');

                 if ($fields == null)
                 {
                     // use all fields except primaryKey, revision, created and modified
                     foreach ($model->data[$model->name] as $key => $val)
                     {
                         if (!in_array($key, array('revision','created','modified',$model->primaryKey)))
                         {
                             $fields[] = $key;
                         }
                     }
                 } elseif (!is_array($fields)) {
                     // single field
                     $fields = array($fields);
                 }

                 // check if this is a new record rather than an update (ie: dont diff the data)
                 if (empty($model->data[$model->name][$model->primaryKey]) && empty($model->data[$model->primaryKey]))
                 {
                     return true;
                 }

                 // get old data
                 $model->recursive = -1;
                 $old_data = $model->find(array($model->primaryKey => $model->data[$model->name][$model->primaryKey]));

                 $nl = '#**!)@#';

                 $output = array();
                 $revised = array();

                 foreach ($fields as $field)
                 {
                     if (@md5($old_data[$model->name][$field]) !== @md5($model->data[$model->name][$field]))
                     {
                         // we have a change
                        $output = @Set::merge(array($field=>inline_diff($old_data[$model->name][$field], $model->data[$model->name][$field], $nl)), $output);

						$revised[] = $field;
                     } else {
					    // keep old revision
					   //$output = @Set::merge(array($field=>$old_data[$model->name][$field]), $output);
					}
                 }

                 if (!empty($revised))
                 {
                     $output['diffable_revised'] = $revised;

                     // save the diff
                     $content = serialize($output);

                     $revision = $this->_increment_revision(&$model,$model->data[$model->name][$model->primaryKey]);

                     $model->Diff->save(array(
                                       'class'=>$model->name,
										'userId' => 'Anonymous',
                                       'foreignId'=>$model->data[$model->name][$model->primaryKey],
                                       'content'=>$content,
                                       'revision'=>$revision));
                 }
             }
             return true;
         }

         function rollback(&$model, $diff_id)
         {
             // get the diff
             $model->Diff->recursive = -1;
             $data = $model->Diff->findByAuditId($diff_id);

             if (!empty($data))
             {
                 $fields = unserialize($data['Audit']['content']);

                 foreach($fields as $key => $value)
                 {
                     // remove <del> and </del> tags and anything in between

                     $fields[$key] = preg_replace('/<del>.*?<\/del>/','',$fields[$key]);

                     // and remove <ins> and </ins> leaving the enclosed string

                     $fields[$key] = preg_replace('/<ins>/','',$fields[$key]);
                     $fields[$key] = preg_replace('/<\/ins>/','',$fields[$key]);
                 }

                 $fields = Set::merge(array($model->primaryKey =>$data['Audit']['foreign_id']),$fields);
				 unset($fields['diffable_revised']);
				
				 $data[$model->name] = $fields;
                 if ($model->save($data))
                 {
                     return $fields;
                 }
             }
             return false;
         }

         function _increment_revision(&$model, $id)
         {
             $model->query("UPDATE {$model->useTable} SET revision = revision + 1 WHERE {$model->primaryKey} = '{$id}'");

             return $this->_get_revision(&$model, $id);
         }

         function _get_revision(&$model, $id)
         {
             $model->recursive = -1;

             $data = $model->find(array($model->name.'.'.$model->primaryKey =>$id), array('revision'));

             return $data[$model->name]['revision'];
         }
     }
?>