<?php
	require_once(LIBS_MAIN_PATH . 'Users.class.php');
	$Users = new Users;
	
	$module_name = 'users';
	$smarty->assign('module_name', $module_name);

	switch ($url['_action_']) {
	
	    // Login
	    case 'login':
	
	        $redirect_to = $_SERVER['HTTP_REFERER'];
	        $success = false;
	
	        $smarty->assign('ref_url', $_SERVER['HTTP_REFERER']);
	
	        if (trim($url['email']) && trim($url['password'])) {
	            $data = $Users->get_item_email($url['email']);
	            if ($data) {
	                if ($data['is_active'] && $data['rec_password'] == md5(trim($url['password']))) {
	                    $Users->item_to_session($data);
	                    $Users->item_to_cookie($data);
	                    $Users->update_item_login($data['id']);
	
	                    if ($data['admin']) {
	                       $redirect_to = '/admin.php';
	                        setcookie('site_admin', true, time() + 60 * 60 * 24 * 365, '/');
	                    }
	                    $success = true;
	                }
	                else if (!$data['is_active']) {
	                    $_SESSION['main_messages'] = $Translate->get_item('error user blocked');
	                }
	                else {
	                    $_SESSION['main_messages'] = $Translate->get_item('error wrong username or password') . ' &nbsp;';
	                }
	            }
	            else {
	                $_SESSION['main_messages'] = $Translate->get_item('error wrong username or password') . ' &nbsp;&nbsp;';
	            }
	        }
	        else {
	            $_SESSION['main_messages'] = $Translate->get_item('error wrong username or password') . ' &nbsp;&nbsp;&nbsp;';
	        }
	
	        if ($success) {
	            if ($url['ref_url']) {
	                $redirect_to = $url['ref_url'];
	            }
	        }
	        else {
	            if ($url['ref_url']) {
	                $_SESSION['ref_url'] = $url['ref_url'];
	            }
	        }
	
	        if ($url['ajax']) {
	            if (!$_SESSION['user']['id']) {
	                $ajax = array(
	                    'messages' => array('error' => $_SESSION['main_messages']),
	                );
	                unset($_SESSION['main_messages']);
	            }
	            else {
	                $ajax['redirect'] = $redirect_to;
	            }
	            echo json_encode($ajax);
	            die();
	        }
	        else {
	            Location($redirect_to);
	            die();
	        }
	
	        break;
	
	
	    // Logout
	    case 'logout':
	        $Users->item_to_session();
	        $Users->item_to_cookie();
	
	        Location($_SERVER['HTTP_REFERER']);
	        die();
	        break;
	
	
	    // Login form
	    case 'loginForm':
	        $ajax = $url['ajax'];
	        if ($ajax) {
	            $syspage['popup'] = true;
	        }
	
	        if (!$_SESSION['user']['id']) {
	            // Page description
	            $syspage['page_title'] = array(
	                $Translate->get_item('login'),
	                $Translate->get_item('users')
	            );
		        
		        $mod = my_fetch($module_name . '.loginform.tpl');
	            break;
	        }
	
	        if (!$ajax) {
		        if ($_SESSION['user']['admin']) {
			        Location('/admin.php');
			        die();
		        }
	            Location('/');
	            die();
	        }
	        break;
	
	
	}
	
