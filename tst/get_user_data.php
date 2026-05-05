<?php
	header('Content-Type: application/json');
	
	$uid = $_GET['uid'] ?? '';

// Tavo DB logika (pavyzdys)
// $user = $db->getRow("SELECT name, balance FROM users WHERE card_uid = ?", [$uid]);
	
	if ($uid == "04:a2:b3:c4:d5:e6:f7") { // Tik pavyzdys testui
		echo json_encode([
			'success' => true,
			'name' => 'Jonas Jonaitis',
			'balance' => '25.50'
		]);
	} else {
		echo json_encode(['success' => false]);
	}
    