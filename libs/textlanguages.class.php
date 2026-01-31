<?php
class TextLanguages {


	// Add / Update
	function update_varchar($table_name, $table_id, $keyword, $text, $language=false) {
		global $Translate;

		$oki = true;
		$language = ($language) ? $language : $Translate->language;
		$text = trim($text);
		$oki = (trim($table_name)) ? $oki : false;
		$oki = ((int)$table_id) ? $oki : false;
		$oki = (trim($keyword)) ? $oki : false;

		if (!$oki) {
			return false;
		}

		$sql_where = "
			table_name = '" . addslashes($table_name) . "' AND table_id = '" . addslashes($table_id) . "'
			AND keyword = '" . addslashes($keyword) . "' AND language = '" . addslashes($language) . "'
		";
		$sql_where_array = [
			'table_name'    => $table_name,
			'table_id'      => $table_id,
			'keyword'       => $keyword,
			'language'      => $language,
		];
		$sql = "
			SELECT id
			FROM languages_varchar
			WHERE {$sql_where}
		";
		$text_id = execute_sql_query($sql, 'get one');

		if ($text_id) {
			// update
			if ($text) {
				$sql = make_update_query('languages_varchar', ['text' => $text], $sql_where_array);
				return execute_sql_query($sql, "update");

			// delete
			} else {
				$sql = "DELETE FROM languages_varchar WHERE {$sql_where}";
				return execute_sql_query($sql, "delete");
			}

		} else {
			// add
			$form = $sql_where_array;
			$form['text'] = $text;
			$sql = make_insert_query('languages_varchar', $form);
			return execute_sql_query($sql, "insert");
		}
	}



	// Add / Update
	function update_text($table_name, $table_id, $keyword, $text, $language=false) {
		global $Translate;

		$oki = true;
		$language = ($language) ? $language : $Translate->language;
		$text = trim($text);
		$oki = (trim($table_name)) ? $oki : false;
		$oki = ((int)$table_id) ? $oki : false;
		$oki = (trim($keyword)) ? $oki : false;

		if (!$oki) {
			return false;
		}

		$sql_where = "
			table_name = '" . addslashes($table_name) . "' AND table_id = '" . addslashes($table_id) . "'
			AND keyword = '" . addslashes($keyword) . "' AND language = '" . addslashes($language) . "'
		";
		$sql_where_array = [
			'table_name'    => $table_name,
			'table_id'      => $table_id,
			'keyword'       => $keyword,
			'language'      => $language,
		];
		$sql = "
			SELECT id
			FROM languages_text
			WHERE {$sql_where}
		";
		$text_id = execute_sql_query($sql, 'get one');

		if ($text_id) {
			// update
			if ($text) {
				$sql = make_update_query('languages_text', ['text' => $text], $sql_where_array, ['text']);
				return execute_sql_query($sql, "update");

				// delete
			} else {
				$sql = "DELETE FROM languages_text WHERE {$sql_where}";
				return execute_sql_query($sql, "delete");
			}

		} else {
			// add
			$form = $sql_where_array;
			$form['text'] = $text;
			$sql = make_insert_query('languages_text', $form, ['text']);
			return execute_sql_query($sql, "insert");
		}
	}

}
?>