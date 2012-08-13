<?php 
/**
 * TranslateIt behavior extended from Transalate behavior.
 */
 
App::import('Behavior', 'Translate');

class TranslateItBehavior extends TranslateBehavior {


    /**
     * afterFind Callback
     * Overriding parent Translate behavior afterFind
     *
     * @param array $results
     * @param boolean $primary
     * @return array Modified results
     * @access public
     */
    function afterFind(&$model, $results, $primary) {
        $this->runtime[$model->alias]['fields'] = array();
        $locale = $this->_getLocale($model);
        
        if ( empty($locale) || empty($results) || empty($this->runtime[$model->alias]['beforeFind'])) {
            return $results;
        }
			
        $beforeFind = $this->runtime[$model->alias]['beforeFind'];
		
		$tModel = 'I18n';
		if (!empty($model->translateModel)){
			// $tModel = $model->translateModel;
		}
        foreach ($results as $key=>$row) {
            $results[$key][$model->alias]['locale'] = (is_array($locale)) ? @$locale[0] : $locale;

            foreach ($beforeFind as $field) {
            	// more locales
                if (is_array($locale)) {
                    foreach ($locale as $_locale) {
                        if (!isset($results[$key][$model->alias][$field]) && ! empty($results[$key][$tModel.'__'.$field.'__'.$_locale]['content'])) {
                            $results[$key][$model->alias][$field] = $results[$key][$tModel.'__'.$field.'__'.$_locale]['content'];
                        }
                        unset($results[$key][$tModel.'__'.$field.'__'.$_locale]);
                    }
                    if (!isset($results[$key][$model->alias][$field])) {
                        $results[$key][$model->alias][$field] = '';
                    }
                } 
				/*else {
                    $value = '';
                    if (! empty($results[$key][$tModel.'__'.$field]['content'])) {
                        $value = $results[$key][$tModel.'__'.$field]['content'];
                    }
                    $results[$key][$model->alias][$field] = $value;
                    unset($results[$key][$tModel.'__'.$field]);
                }*/
            }
            // added for fixing result
            if (! empty($row[$tModel])) {
            
                if (! empty($row[0])) {
                    $results[$key][$model->alias] = array_merge($row[0], $row[$tModel]);
                }
                
                if (! empty($row[$model->alias])) {
                    $results[$key][$model->alias] = array_merge($results[$key][$model->alias], $row[$tModel]);
                }
                
                $results[$key][$model->alias]['locale'] = $model->locale;
                unset($results[$key][0]);
				unset($results[$key][$tModel]);
            }
        }
        
        return $results;
    }
    
    /**
     * Replace original callback and call it in beforeSave. This patch make function data manaipulation in beforeValidate Model.
     * @param object $model
     * @return boolean
     */
    function beforeValidate(&$model) {
        return true;
    }
    
    /**
     * Replace original callback and call parent beforeValidate. This patch make function data manipulation in beforeValidate Model.
     * @param object $model
     * @return boolean
     */
    function beforeSave(&$model) {
        return parent::beforeValidate($model);
    }
    /**
     * afterSave callback
     * Call back create all translation fields in i18n tables
     * @param boolean $created
     * @return void
     * @access public
     */
    function afterSave(&$model, $created) {
    
        if (!isset($this->runtime[$model->alias]['beforeSave'])) {
            return true;
        }
        $inlocale = $locale = $this->_getLocale($model);
        $allLocales = Set::classicExtract(Configure::read('Domain.availableLanguagesMap'), '{s}.locale');
        $tempData = $this->runtime[$model->alias]['beforeSave'];
        unset($this->runtime[$model->alias]['beforeSave']);
        $conditions = array('model'=>$model->alias,
                             'foreign_key'=>$model->id);
        $RuntimeModel = &$this->translateModel($model);
        
        if ( empty($allLocales)) {
            $allLocales = array($locale);
        }
        foreach ($allLocales as $locale) {
            foreach ($tempData as $field=>$value) {
                unset($conditions['content']);
                $conditions['field'] = $field;
                if (is_array($value)) {
                    $conditions['locale'] = array_keys($value);
                } else {
                    $conditions['locale'] = $locale;
                    if (is_array($locale)) {
                        $value = array($locale[0]=>$value);
                    } else {
                        $value = array($locale=>$value);
                    }
                }
                $translations = $RuntimeModel->find('list', array('conditions'=>$conditions, 'fields'=>array($RuntimeModel->alias.'.locale', $RuntimeModel->alias.'.id')));
                foreach ($value as $_locale=>$_value) {
                    $conditions['locale'] = $_locale;
                    if ( empty($translations) || $_locale == $inlocale) {
                        unset($translations['content']);
                        $conditions['content'] = $_value;
                    }
                    $RuntimeModel->create();
                    if (array_key_exists($_locale, $translations)) {
                        $RuntimeModel->save(array($RuntimeModel->alias=>array_merge($conditions, array('id'=>$translations[$_locale]))));
                    } else {
                        $RuntimeModel->save(array($RuntimeModel->alias=>$conditions));
                    }
                }
            }
        }
    }
}
