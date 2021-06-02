<?php
/**
 * DokuWiki Plugin structnotification (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Szymon Olewniczak <it@rid.pl>
 */

class admin_plugin_structnotification extends DokuWiki_Admin_Plugin
{
    protected $headers = ['schema', 'field', 'operator', 'value', 'filters', 'users_and_groups', 'message'];
    protected $operators = ['before', 'after', 'at'];

    /**
     * @return int sort number in admin menu
     */
    public function getMenuSort()
    {
        return 499;
    }

    /**
     * @return bool true if only access for superuser, false is for superusers and moderators
     */
    public function forAdminOnly()
    {
        return false;
    }

    /**
     * Should carry out any processing required by the plugin.
     */
    public function handle()
    {
        global $INPUT;
        global $ID;

        try {
            /** @var \helper_plugin_structnotification_db$db_helper */
            $db_helper = plugin_load('helper', 'structnotification_db');
            $sqlite = $db_helper->getDB();
        } catch (Exception $e) {
            msg($e->getMessage(), -1);
            return;
        }

        if ($INPUT->str('action') && $INPUT->arr('predicate') && checkSecurityToken()) {
            $predicate = $INPUT->arr('predicate');
            if ($INPUT->str('action') === 'add') {
                $errors = $this->validate($predicate);
                if ($errors) {
                    $this->display_errors($errors);
                    return;
                }
                $ok = $sqlite->storeEntry('predicate', $predicate);
                if(!$ok) msg('failed to add predicate', -1);
            } elseif($INPUT->str('action') === 'delete') {
                $ok = $sqlite->query('DELETE FROM predicate WHERE id=?', $predicate['id']);
                if(!$ok) msg('failed to delete predicate', -1);
            } elseif($INPUT->str('action') === 'update') {
                $errors = $this->validate($predicate);
                if ($errors) {
                    $this->display_errors($errors);
                    return;
                }

                $set = implode(',', array_map(function ($header) {
                    return "$header=?";
                }, $this->headers));
                $predicate['id'] = $INPUT->str('edit');
                $ok = $sqlite->query("UPDATE predicate SET $set WHERE id=?", $predicate);
                if(!$ok) msg('failed to update predicate', -1);
            }

            if ($ok) send_redirect(wl($ID, array('do' => 'admin', 'page' => 'structnotification'), true, '&'));
        }
    }

    /**
     * Render HTML output, e.g. helpful text and a form
     */
    public function html()
    {
        global $INPUT;
        global $ID;

        try {
            /** @var \helper_plugin_structnotification_db$db_helper */
            $db_helper = plugin_load('helper', 'structnotification_db');
            $sqlite = $db_helper->getDB();
        } catch (Exception $e) {
            msg($e->getMessage(), -1);
            return;
        }

        ptln('<h1>' . $this->getLang('menu') . '</h1>');
        ptln('<table>');

        ptln('<tr>');
        foreach ($this->headers as $header) {
            ptln('<th>' . $this->getLang('admin header '. $header) . '</th>');
        }
        //extra field for buttons
        ptln('<th></th>');
        ptln('</tr>');

        $q = 'SELECT * FROM predicate';
        $res = $sqlite->query($q);

        $predicates = $sqlite->res2arr($res);

        foreach ($predicates as $predicate) {
            if ($INPUT->str('edit') == $predicate['id']) {
                if (!$INPUT->has('predicate')) {
                    $INPUT->set('predicate', $predicate);
                }

                ptln($this->form('update'));
                continue;
            }

            ptln('<tr>');
            foreach ($this->headers as $header) {
                $value = $predicate[$header];
                if ($header == 'message') {
                    $html = p_render('xhtml',p_get_instructions($value), $info);
                    ptln('<td>' . $html . '</td>');
                } else {
                    ptln('<td>' . $value . '</td>');
                }
            }

            ptln('<td>');
            $link = wl(
                $ID, array(
                    'do' => 'admin',
                    'page' => 'structnotification',
                    'edit' => $predicate['id']
                )
            );
            ptln('<a href="' . $link . '">'.$this->getLang('edit').'</a> | ');

            $link = wl(
                $ID, array(
                    'do' => 'admin',
                    'page' => 'structnotification',
                    'action' => 'delete',
                    'sectok' => getSecurityToken(),
                    'predicate[id]' => $predicate['id']
                )
            );
            ptln('<a class="plugin__structnotification_delete" href="' . $link . '">'.$this->getLang('delete').'</a>');

            ptln('</td>');
            ptln('</tr>');
        }
        if (!$INPUT->has('edit')) {
            ptln($this->form());
        }
        ptln('</table>');
    }

    protected function form($action='add')
    {
        global $ID;

        $form = new dokuwiki\Form\Form();
        $form->addTagOpen('tr');

        $form->addTagOpen('td');
        $form->addTextInput('predicate[schema]')->attr('style', 'width: 8em');
        $form->addTagClose('td');

        $form->addTagOpen('td');
        $form->addTextInput('predicate[field]')->attr('style', 'width: 8em');
        $form->addTagClose('td');

        $form->addTagOpen('td');
        $form->addDropdown('predicate[operator]', $this->operators);
        $form->addTagClose('td');

        $form->addTagOpen('td');
        $form->addTextInput('predicate[value]')->attr('style', 'width: 12em');
        $form->addTagClose('td');

        $form->addTagOpen('td');
        $form->addTextarea('predicate[filters]')->attr('style', 'width: 12em; height: 5em;');
        $form->addTagClose('td');

        $form->addTagOpen('td');
        $form->addTextInput('predicate[users_and_groups]')->attr('style', 'width: 12em');
        $form->addTagClose('td');

        $form->addTagOpen('td');
        $form->addHTML('<div id="tool__bar"></div>');
        $form->setHiddenField('id', $ID); //for linkwiz
        $form->addTextarea('predicate[message]')
            ->id('wiki__text')
            ->attr('style', 'width: 100%; height: 10em;');
        $form->addTagClose('td');

        $form->addTagOpen('td');
        $form->addButton('action', $this->getLang($action))->val($action);
        $link = wl(
            $ID, [
                'do' => 'admin',
                'page' => 'structnotification',
            ]
        );
        $cancel_link = '<a href="' . $link . '" style="margin-left:1em" id="plugin__structnotification_cancel">' . $this->getLang('cancel') . '</a>';
        $form->addHTML($cancel_link);
        $form->addTagClose('td');


        $form->addTagClose('tr');

        return $form->toHTML();
    }

    protected function validate($predicate)
    {
        $errors = [];
        if (blank($predicate['schema'])) {
            $errors[] = 'val schema blank';
        }

        if (blank($predicate['field'])) {
            $errors[] = 'val field blank';
        }

        if (blank($predicate['operator'])) {
            $errors[] = 'val operator blank';
        }

        if (blank($predicate['value'])) {
            $errors[] = 'val value blank';
        }

        if (blank($predicate['users_and_groups'])) {
            $errors[] = 'val users_and_groups blank';
        }

        if (blank($predicate['message'])) {
            $errors[] = 'val message blank';
        }

        return $errors;
    }

    protected function display_errors($errors) {
        foreach ($errors as $error) {
            $msg = $this->getLang($error);
            if (!$msg) $msg = $error;
            msg($error, -1);
        }
    }
}

