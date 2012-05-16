<?php
//require_once 'adminvars.php';

/**
 * Description of Ewind_AdminEngine
 *
 * @author e-wind
 */

class Ewind_AdminEngine {

    /**
     *
     * @var Ewind_DBEngine
     */
    private $dbe;
    /**
     *
     * @var array
     */
    public $data;
    public function __construct($dbe) {
        $this->dbe = $dbe;
        $this->data['cat_id'] = '';
        $this->data['path'] = '';
        $this->data['name'] = '';
        $this->data['title'] = '';
        $this->data['descr'] = '';
        $this->data['content'] = '';
        $this->data['state'] = '';
        $this->data['serialnum'] = '';
        $this->data['type'] = '';
        $this->data['time'] = '';
        $this->data['tags'] = '';
    }


    public function getData($categ_id, $entry_id, $addnew, $tagedit) {
        if ($categ_id) { //if categ exist, get data for category
            $content = $this->dbe->getContent($_GET['categ_id']);
            if ($content == NULL) { //if data not exist in DB, go to previous URL
                header("Location:".$_SERVER['REDIRECT_URL']);
                exit();
            }
            $this->data['about'] = 'Редактировать категорию: '.$content['cat_name'];
            if ($entry_id) {//existing category & existing entry &
                $this->data['about'] = 'Редактировать запись в категории: '.$content['cat_name'];
                if (!isset ($_POST['time'])) {
                     //if not posted content init entry content
                    $content = $this->dbe->getContent($categ_id, $entry_id);
                    if ($content == NULL) { //if data not exist in DB, go to previous URL
                        header("Location:".$_SERVER['REDIRECT_URL']);
                        exit();
                    }
                    $this->data['cat_id'] = $content['cat_id'];
                    $this->data['path'] = $content['entry_path'];
                    $this->data['name'] = $content['entry_name'];
                    $this->data['title'] = $content['entry_title'];
                    $this->data['descr'] = $content['entry_descr'];
                    $this->data['content'] = $content['entry_content'];
                    $this->data['state'] = $content['entry_state'];
                    $this->data['type'] = $content['entry_type'];
                    $this->data['time'] = date('d.m.y', $content['entry_time']);
                    $this->data['tags'] = implode(',', $content['tag_list']);
                }
            }
            elseif ($addnew) { //create new entry in existing category
                $this->data['about'] = 'Создать новую запись в категории: '.$content['cat_name'];
                $this->data['cat_id'] = $categ_id;
            }
            elseif (!isset($_POST['serialnum'])) {
                //not new & not old entry & categ exist & not posted content
                //init categ content
                $this->data['path'] = $content['cat_path'];
                $this->data['name'] = $content['cat_name'];
                $this->data['title'] = $content['cat_title'];
                $this->data['descr'] = $content['cat_descr'];
                $this->data['state'] = $content['cat_state'];
                $this->data['serialnum'] = $content['cat_serial_num'];
            }
        }
        elseif ($addnew) { //if not exist, create new category
            $this->data['about'] = 'Создать новую категорию';
            //init empty
        }
        elseif ($tagedit) { //if editing tags
            $this->data['about'] = 'Редактировать теги';
        }
        else { //not new & not existing category
            $this->data['about'] = "Ничего не выбрано";
        }
        //save posted content
        if (isset($_POST['cat_id'])) $this->data['cat_id'] = $_POST['cat_id'];
        if (isset($_POST['path'])) $this->data['path'] = $_POST['path'];
        if (isset($_POST['name'])) $this->data['name'] = $_POST['name'];
        if (isset($_POST['title'])) $this->data['title'] = $_POST['title'];
        if (isset($_POST['descr'])) $this->data['descr'] = $_POST['descr'];
        if (isset($_POST['content'])) $this->data['content'] = $_POST['content'];
        if (isset($_POST['state'])) $this->data['state'] = $_POST['state'];
        if (isset($_POST['serialnum'])) $this->data['serialnum'] = $_POST['serialnum'];
        if (isset($_POST['type'])) $this->data['type'] = $_POST['type'];
        if (isset($_POST['time'])) $this->data['time'] = $_POST['time'];
        if (isset($_POST['tags'])) $this->data['tags'] = preg_replace('/[^0-9a-z_,]/i', '', $_POST['tags']);
        //save posted tags data
        if (isset($_POST['alltags'])) {
            $this->data['alltags'] = $this->dbe->getTags();
            //check if tags modified an set mod status
            for ($i = 0; $i < count($_POST['alltags']); $i++) {
                $oldtag = $this->data['alltags'][$i];
                $this->data['alltags'][$i] = $_POST['alltags'][$i];
                if ($oldtag['tag_name'] !== $_POST['alltags'][$i]['tag_name']){
                    $this->data['alltags'][$i]['mod'] = 1;
                }
                else {
                    $this->data['alltags'][$i]['mod'] = 0;
                }
            }

        }
        else { //if not posted tags
            //init tags data
            $this->data['alltags'] = $this->dbe->getTags();
        }

        //return $this->data;
    }

    public function doIt($categ_id, $entry_id, $addnew, $tagedit) {
        $respond['message'] = "OK";
        $respond['confirm'] = FALSE;
        $respond['delete'] = FALSE;
        if (isset($_POST['save'])) {
            $respond['confirm'] = TRUE;
            $respond['message'] = "Сохранить?";
            if ($tagedit) {
                $respond['message'] .= "\nБудут изменены и удалены помеченные теги.";
            }
            if ($categ_id && ($entry_id xor $addnew)) {
                //check entry data
                $respond['message'] .= "\nБудут сохранены теги:";
                foreach (explode(',', $this->data['tags']) as $tag) {
                    $respond['message'] .= "\n ".$tag;
                }
            }
            if (!$entry_id && ($categ_id xor $addnew)) {
                //check categ data
                if (empty($this->data['path'])) {
                    $respond['confirm'] = FALSE;
                    $respond['message'] .= "\n Ошибка: Не заполнено поле 'Путь'.";
                }
                if (empty($this->data['serialnum']) || !is_numeric($this->data['serialnum'])) {
                    $respond['confirm'] = FALSE;
                    $respond['message'] .= "\n Ошибка: Не заполнено или не число в поле 'Порядковый номер'.";
                }
            }
        }

        if (isset($_POST['delete'])) {
            $respond['confirm'] = TRUE;
            if ($categ_id && ($entry_id xor $addnew)) {
                //check delete entry entry_id
                $respond['message'] = 'Удалить запись??';
            }
            if ((!$entry_id && ($categ_id xor $addnew))) {
                $respond['message'] = 'Удалить категорию?? Все записи в категории будут потеряны!!!';
            }
            $respond['delete'] = TRUE;
        }

        if (isset($_POST['OK'])) {
            if (isset($_POST['del_flag'])) {
                $this->dbe->deletePage($categ_id, $entry_id);
            }
            else {
                if ($addnew) {
                    // 'insert categ or entry data categ_id';
                    $this->dbe->addPage($this->data, $categ_id);
                }
                else {
                    // 'update entry OR categ categ_id entry_id';
                    $this->dbe->updatePage($this->data, $categ_id, $entry_id);
                }
            }
            if ($tagedit) {
                $tagIDsToDelete = array();
                $tagsToUpdate = array();
                $doDelete = FALSE;
                $doUpdate = FALSE;
                for ($i = 0; $i < count($this->data['alltags']); $i++) {
                    if ($this->data['alltags'][$i]['del']) {
                        $tagIDsToDelete[] = $this->data['alltags'][$i]['tag_id'];
                        $doDelete = TRUE;
                    }
                    if ($this->data['alltags'][$i]['mod']){
                        $tagsToUpdate[] = $this->data['alltags'][$i];
                        $doUpdate = TRUE;
                    }
                }
                if ($doDelete) {
                    $this->dbe->deleteTags($tagIDsToDelete);
                }
                if ($doUpdate) {
                    $this->dbe->updateTags($tagsToUpdate);
                }
            }
            header("Location:".$_SERVER['HTTP_REFERER']);
            exit();
        }
        return $respond;
    }
}

?>
