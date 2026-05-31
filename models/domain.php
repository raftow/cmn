<?php


class Domain extends AFWObject
{

      public static $DATABASE            = "";
      public static $MODULE                = "cmn";
      public static $TABLE                  = "";
      public static $DB_STRUCTURE = null;

      public static $DOMAIN_GENERAL = 1;                                    // المجالات العامة - general                                      
      public static $DOMAIN_STUDENT_INFORMATION_SYSTEM = 2;                 // إدارة الطلاب والمدارس - education                                  
      public static $DOMAIN_PAG = 3;                                        // تحليل وتصميم النظم - system analysis & design                     
      public static $DOMAIN_TVT = 4;                                        //التدريب التقني - TVT                                               
      public static $DOMAIN_IT_SUPPORT = 5;                                 //صيانة النظم - IT - system support                           
      public static $DOMAIN_PLANNING = 6;                                   //التخطيط - Planning                                            
      public static $DOMAIN_BUDGET = 7;                                     //الميزانية - Budget                                              
      public static $DOMAIN_HEALTH = 8;                                     //الصحة - Health                                                  
      public static $DOMAIN_IT_USER_MANAGEMENT_SYSTEM = 9;                  //الصلاحيات على التطبيقات - IT-UMS                                
      public static $DOMAIN_HUMAN_RESSOURCE = 10;                           //الموارد البشرية - Human Ressource                                  
      public static $DOMAIN_MEDIA_AND_PUBLIC_RELATIONS = 11;                //الإعلام والعلاقات العامة - Public Relations                       
      public static $DOMAIN_BUSINESS_MANAGEMENT_SYSTEM = 12;                //إدارة الأعمال - Business                                           
      public static $DOMAIN_CRM = 13;                                       //خدمة العملاء - Customer service                                   
      public static $DOMAIN_TRANSPORT = 14;                                 //النقل والمواصلات - transportation                           
      public static $DOMAIN_TRAVEL = 15;                                    //عالم الأسفار - Travel company                                 
      public static $DOMAIN_SUMMER_TRAINING = 20;                           //التدريب الصيفي - summer training                               
      public static $DOMAIN_EXPERIENCE_SHARING = 16;                      // تبادل الخبرات وادارتها - share exp-self dev                                
      public static $DOMAIN_RESOURCES_AND_ASSETS = 17;                    //الموارد والأصول - resources & assets                              
      public static $DOMAIN_CONTENT_MANAGEMENT_SYSTEM = 18;                 //إدارة المحتوى - Content Management                                
      public static $DOMAIN_MANAHEJ = 19;                                   //المناهج التعليمية - Studies programs                          
      public static $DOMAIN_TALENT = 21;                                    //المسابقات - Competitions                                       
      public static $DOMAIN_CARD = 22;                                      //طباعة بطاقات الاشتراك - member cards print                       
      public static $DOMAIN_ENQ_COMMON = 23;                                //البحوث والدراسات - Research and Studies                    
      public static $DOMAIN_LICENSE = 24;                                   //إدارة التراخيص - licenses management                          
      public static $DOMAIN_ADMISSION = 25;                                 //التسجيل والقبول - application & admission                     

      public function __construct()
      {
            parent::__construct("domain", "id", "cmn");
            $this->QEDIT_MODE_NEW_OBJECTS_DEFAULT_NUMBER = 5;
            $this->DISPLAY_FIELD = "domain_name_ar";
            $this->ORDER_BY_FIELDS = "domain_name_ar";
            $this->editByStep = true;
            $this->editNbSteps = 3;
            $this->UNIQUE_KEY = array('domain_code');
            $this->ENABLE_DISPLAY_MODE_IN_QEDIT = true;
      }

      public static function loadById($id)
      {
            $obj = new Domain();
            $obj->select_visibilite_horizontale();
            if ($obj->load($id)) {
                  return $obj;
            } else return null;
      }



      public static function loadByMainIndex($domain_code, $create_obj_if_not_found = false)
      {
            if (!$domain_code) throw new AfwRuntimeException("loadByMainIndex : domain_code is mandatory field");


            $obj = new Domain();
            $obj->select("domain_code", $domain_code);

            if ($obj->load()) {
                  if ($create_obj_if_not_found) $obj->activate();
                  return $obj;
            } elseif ($create_obj_if_not_found) {
                  $obj->set("domain_code", $domain_code);

                  $obj->insertNew();
                  if (!$obj->id) return null;                                    // means beforeInsert rejected insert operation
                  $obj->is_new = true;
                  return $obj;
            } else return null;
      }


      public static function loadByCodes($object_code_arr, $create_if_not_exists_with_name = "", $lang = "ar", $rename_if_exists = false)
      {
            if (count($object_code_arr) != 1) die("Domain::loadByCodes : only one domain_code is needed : given " . var_export($object_code_arr, true));
            $domain_code = $object_code_arr[0];
            $obj = self::loadByMainIndex($domain_code, $create_if_not_exists_with_name);
            if (($obj->is_new) or $rename_if_exists) {
                  if ($lang == "ar") $obj->set("titre_short", $create_if_not_exists_with_name);
                  if ($lang == "en") $obj->set("titre_short_en", $create_if_not_exists_with_name);
                  $obj->commit();
            }

            return $obj;
      }

      public function getDisplay($langue = "ar")
      {
            $lang = AfwLanguageHelper::getGlobalLanguage();
            if (!$langue)   $langue = $lang;
            if (!$langue)   $langue = "ar";
            $data = array();
            $link = array();

            list($data["ar"], $link["ar"]) = $this->displayAttribute("short_name_ar", false, $lang);
            list($data["en"], $link["en"]) = $this->displayAttribute("short_name_en", false, $lang);


            return $data[$langue];
      }

      protected function getOtherLinksArray($mode, $genereLog = false, $step = "all")
      {
            $lang = AfwLanguageHelper::getGlobalLanguage();
            $objme = AfwSession::getUserConnected();
            $me = ($objme) ? $objme->id : 0;

            $otherLinksArray = $this->getOtherLinksArrayStandard($mode, false, $step);
            $my_id = $this->getId();
            $displ = $this->getDisplay($lang);

            if ($mode == "mode_jobroleList") {
                  unset($link);
                  $my_id = $this->getId();
                  $link = array();
                  $title = "إدارة الصلاحيات  الوظيفية ";
                  $title_detailed = $title . "لـ : " . $displ;
                  $link["URL"] = "main.php?Main_Page=afw_mode_qedit.php&cl=Jobrole&currmod=ums&id_origin=$my_id&class_origin=Domain&module_origin=cmn&newo=10&limit=30&ids=all&fixmtit=$title_detailed&fixmdisable=1&fixm=id_domain=$my_id&sel_id_domain=$my_id";
                  $link["TITLE"] = $title;
                  $link["UGROUPS"] = array();
                  $otherLinksArray[] = $link;
            }

            if ($mode == "mode_goalList") {
                  unset($link);
                  $my_id = $this->getId();
                  $link = array();
                  $title = "إضافة هدف جديد";
                  $title_detailed = $title . "لـ : " . $displ;
                  $link["URL"] = "main.php?Main_Page=afw_mode_edit.php&cl=Goal&currmod=bau&id_origin=$my_id&class_origin=Domain&module_origin=cmn&sel_domain_id=$my_id";
                  $link["TITLE"] = $title;
                  $link["UGROUPS"] = array();
                  $otherLinksArray[] = $link;
            }

            /*
            if ($mode == "mode_goalList") {
                  unset($link);
                  $my_id = $this->getId();
                  $link = array();
                  $title = "إدارة الأهداف ";
                  $title_detailed = $title . "لـ : " . $displ;
                  $link["URL"] = "main.php?Main_Page=afw_mode_qedit.php&cl=Goal&currmod=bau&id_origin=$my_id&class_origin=Domain&module_origin=cmn&newo=3&limit=30&ids=all&fixmtit=$title_detailed&fixmdisable=1&fixm=domain_id=$my_id&sel_domain_id=$my_id";
                  $link["TITLE"] = $title;
                  $link["UGROUPS"] = array();
                  $otherLinksArray[] = $link;
            }*/



            return $otherLinksArray;
      }

      protected function getPublicMethods()
      {

            $pbms = array();

            $color = "green";
            $title_ar = "توليد الصلاحيات النموذجية";
            $pbms["xab5cB"] = array(
                  "METHOD" => "createStandardJobResp",
                  "COLOR" => $color,
                  "LABEL_AR" => $title_ar,
                  "ADMIN-ONLY" => true,
                  "BF-ID" => ""
            );

            $color = 'blue';
            $title_ar = "توليد الأهداف الأصلية";
            $methodName = 'generateOriginalGoals';
            $pbms[AfwStringHelper::hzmEncode($methodName)] =
                  array(
                        'METHOD' => $methodName,
                        'COLOR' => $color,
                        'LABEL_AR' => $title_ar,
                        'ADMIN-ONLY' => true,
                        'BF-ID' => '',
                        'TITLE-LENGTH' => 72,
                        // 'STEP' => $this->stepOfAttribute('employee_id')
                  );


            return $pbms;
      }

      public function generateOriginalGoals($lang = "ar")
      {
            $jobroleList = $this->get("jobroleList");
            $objModule = $this->calcMainApplication();
            if (!$objModule or (!$objModule->id)) return ["generateOriginalGoals : main application not found", ""];
            $objModule_id = $objModule->id;
            $system_id = $objModule->getVal("id_system");
            $nb_add = 0;
            $nb_upd = 0;
            foreach ($jobroleList as $jobroleItem) {
                  $object_name_ar = $jobroleItem->getVal("titre_short");
                  $object_name_en = $jobroleItem->getVal("titre_short_en");
                  $object_title_ar = $jobroleItem->getVal("titre");
                  $object_title_en = $jobroleItem->getVal("titre_en");

                  $jobrole_code = $jobroleItem->getVal("jobrole_code");
                  if (AfwStringHelper::stringStartsWith($jobrole_code, "jr-")) {
                        $goal_code = substr($jobrole_code, 3);
                        $objGoal = Goal::loadByMainIndex($system_id, $objModule_id, $goal_code, true);
                        if (!$objGoal) return ["generateOriginalGoals : Goal creation with loadByMainIndex($system_id,$objModule_id, $goal_code, true) failed", ""];

                        if ($objGoal->is_new) $nb_add++;
                        else $nb_upd++;
                        $objGoal->set('goal_type_id', Goal::$GOAL_TYPE_JOB_RESPONSIBILITY_GOAL);
                        $objGoal->set('domain_id', $this->id);
                        $objGoal->set('goal_name_en', $object_name_en);
                        $objGoal->set('goal_name_ar', $object_name_ar);
                        $objGoal->set('goal_desc_en', $object_title_en);
                        $objGoal->set('goal_desc_ar', $object_title_ar);

                        $objGoal->set('jobrole_id', $jobroleItem->id);
                        $objGoal->commit();
                  }
            }

            return ["", "$nb_add goal(s) added and $nb_upd goal(s) updated"];
      }


      public function createStandardJobResp($lang = "ar")
      {
            $cjr = $this->getCommonJobResp($create_obj_if_not_found = true, $always_update_name = true);
            list($djr, $dataGoalObj, $d_message) = $this->getDataJobResp($create_obj_if_not_found = true, $always_update_name = true);
            list($ljr, $lkpGoalObj, $l_message) = $this->getLookupJobResp($create_obj_if_not_found = true, $always_update_name = true);

            $info = "";
            $err = "";

            if ($ljr) list($err, $info) = $ljr->genereMainGoal($lang);

            if ($cjr) {
                  if ($cjr->is_new) $info .= "$cjr created <br>";
                  else $info .= "$cjr exists already <br>";
            } else $err .= "Common Job Resp not created <br>";

            if ($djr) {
                  if ($djr->is_new) $info .= "$djr created <br>";
                  else $info .= "$djr exists already <br>";
            } else $err .= "Data Job Resp not created : $d_message<br>";

            if ($ljr) {
                  if ($ljr->is_new) $info .= "$ljr created <br>";
                  else $info .= "$ljr exists already <br>";
            } else $err .= "Lookup Job Resp not created : $l_message<br>";

            return array($err, $info);
      }

      public function getCommonJobResp($create_obj_if_not_found = true, $always_update_name = false)
      {
            $domain_code = $this->getVal("domain_code");
            if (!$domain_code) return null;
            $code_jr = "common-" . strtolower($domain_code);


            $file_dir_name = dirname(__FILE__);

            $jrObj = Jobrole::loadByMainIndex($this->getId(), $code_jr, $create_obj_if_not_found);
            if ($jrObj->is_new or $always_update_name) {
                  $jrObj->set("titre_en", $this->getShortDisplay("en") . " employee common responsibility");
                  $jrObj->set("titre", "صلاحيات موظف " . $this->getShortDisplay("ar"));
                  $jrObj->set("titre_short_en", $this->getShortDisplay("en") . " employee  ");
                  $jrObj->set("titre_short", "موظف " . $this->getShortDisplay("ar"));
                  $jrObj->update();
            }


            return $jrObj;
      }

      public function getDataJobResp($create_obj_if_not_found = true, $always_update_name = false)
      {
            $lang = AfwLanguageHelper::getGlobalLanguage();
            /**
             * @var Module $mainApplication
             */
            $mainApplication = $this->calcMainApplication();
            if (!$mainApplication) return array(null, null, "no main application defined");
            if ($mainApplication->getVal("id_pm") != $this->getId()) return array(null, null, "id of domain in main application is different than this DOMAIN-ID");

            list($jrObj, $goalObj) = $mainApplication->getDataJobResp($create_obj_if_not_found, $always_update_name);

            return array($jrObj, $goalObj, "");
      }

      public function getLookupJobResp($create_obj_if_not_found = true, $always_update_name = false)
      {
            $lang = AfwLanguageHelper::getGlobalLanguage();

            /**
             * @var Module $mainApplication
             */

            $mainApplication = $this->calcMainApplication();
            if (!$mainApplication) return array(null, null, "no main application defined");
            if ($mainApplication->getVal("id_pm") != $this->getId()) return array(null, null, "id of domain in main application is different than this DOMAIN-ID");

            list($jrObj, $goalObj) = $mainApplication->getLookupJobResp($create_obj_if_not_found, $always_update_name);

            return array($jrObj, $goalObj, "");
      }

      public function getRAMObjectData()
      {
            $category_id = 13;

            $type_id = 1204;

            /*$code = $this->getVal("goal_code");
                  if(!$code)*/

            $code = "domain-" . $this->getId();

            $name_ar = $this->getVal("domain_name_ar");
            $name_en = $this->getVal("domain_name_en");
            $specification = $this->getVal("domain_name_ar") . "\n------- english : ---------\n" . $this->getVal("domain_name_en");

            $childs = array();
            //$childs[3] =  $this->get("jobroleList");


            return array($category_id, $type_id, $code, $name_ar, $name_en, $specification, $childs);
      }


      public function beforeMAJ($id, $fields_updated)
      {
            $lang = AfwLanguageHelper::getGlobalLanguage();


            if ((!$this->getVal("domain_code")) or ($this->getVal("domain_code") == "--")) {
                  $this->set("domain_code", strtoupper(AfwStringHelper::javaNaming($this->getVal("domain_name_en"))));
            }

            if ((!$this->getVal("short_name_ar")) or ($this->getVal("short_name_ar") == "--")) {
                  $this->set("short_name_ar", $this->getVal("domain_name_ar"));
            }


            if ((!$this->getVal("short_name_en")) or ($this->getVal("short_name_en") == "--")) {
                  $this->set("short_name_en", $this->getVal("domain_name_en"));
            }

            return true;
      }


      public function calcMainApplication($what = 'object')
      {
            $application_code = strtolower($this->getVal("application_code"));
            $domain_code = strtolower($this->getVal("domain_code"));
            if (!$application_code) {
                  $application_code = $domain_code . "u";
            }

            $mainApplication = new Module();


            $mainApplication->clearSelect();
            $mainApplication->where("avail='Y' and id_module_type = 5 and (module_code='$application_code' or module_code='$domain_code')");
            if (!$mainApplication->load()) $mainApplication = null;

            return AfwLoadHelper::giveWhat($mainApplication, $what);
      }


      /**
       * @param int $atable_id
       */
      public function tableIsManagedByAtLeastOneGoal($atable_id)
      {
            $goalList = $this->get("goalList");
            /**
             * @var Goal $goalItem
             */
            foreach ($goalList as $goalItem) {
                  if ($goalItem->tableIsManaged($atable_id)) return true;
            }

            return false;
      }


      public function calcNon_managed_tables($what = "value")
      {
            /**
             * @var Module $mainApplication
             */

            $mainApplication = $this->calcMainApplication();
            if (!$mainApplication) return "no main application defined";

            $return_html = "";
            $table_html = "";
            $lookup_html = "";
            // tables
            $tableList = $mainApplication->get("tbs");
            foreach ($tableList as $tableItem) {
                  $table_html .= $tableItem->getVal("atable_name") . ", ";
            }

            // lookups
            $lookupList = $mainApplication->get("lkps");
            foreach ($lookupList as $tableItem) {
                  $lookup_html .= $tableItem->getVal("atable_name") . ", ";
            }

            if ($table_html) $return_html .= "TABLES NOT MANAGED : $table_html <BR>\n";
            if ($lookup_html) $return_html .= "LOOKUPS NOT MANAGED : $lookup_html <BR>\n";
            if (!$return_html) $return_html = "well done all is managed";

            return $return_html;
      }
}
