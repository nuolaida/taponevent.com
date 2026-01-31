<?php
	require_once(LIBS_MAIN_PATH . 'Users.class.php');
	$Users = new Users;
	
	$module_name = 'users';
	$smarty->assign('module_name', $module_name);
	
	switch ($page_action) {
	
	    case 'loginAct':
	        $success = false;
	        if (trim($url['email']) && trim($url['password'])) {
	            $data = $Users->get_item_email($url['email']);
	            if ($data) {
	                if ($data['is_active'] && $data['rec_password'] == md5(trim($url['password']))) {
	                    $Users->item_to_session($data);
	                    $Users->update_item_login($data['id']);
		                
		                if (get_side() == 'app') {
			                Location('/app.php');
			                die();
		                }
		                
		                if ($data['admin']) {
			                Location('/admin.php');
			                die();
			                //setcookie('site_admin', true, time() + 60 * 60 * 24 * 365, '/');
		                }
	                    $success = true;
	                } else if (!$data['is_active']) {
	                    $_SESSION['main_messages'][] = $Translate->get_item('error user blocked');
	                } else {
	                    $_SESSION['main_messages'][] = $Translate->get_item('error wrong username or password');
	                }
	            } else {
	                $_SESSION['main_messages'][] = $Translate->get_item('error wrong username or password');
	            }
	        } else {
	            $_SESSION['main_messages'][] = $Translate->get_item('error wrong username or password');
	        }
	
			if (get_side() == 'app') {
				Location('/app.php');
				die();
			}
			
            Location('index.php');
            die();
	        break;
	
	
	    case 'logoutAct':
	        $Users->item_to_session();
	        $Users->item_to_cookie();
	
	        Location('/app.php');
	        die();
	        break;
		
		
		case 'login':
		case 'loginForm':
		default:
	        if ($_SESSION['user']['id']) {
		        Location('/app.php');
		        die();
			}
			$template_name = $module_name . '.login';
	        break;
	}

