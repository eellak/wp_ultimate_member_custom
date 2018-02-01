<?php

	/***
	***	@profile name update
	***/
	add_action('um_update_profile_full_name', 'um_update_profile_full_name' );
	function um_update_profile_full_name( $changes ) {
		global $ultimatemember;

		
		// Sync display name changes
		$option = um_get_option('display_name');
		
		$user_id = $ultimatemember->user->id;

		if( ! isset( $user_id ) || empty( $user_id ) ){
			$user = get_user_by( 'email', $changes['user_email'] );
			um_fetch_user( $user->ID );
			$user_id = $user->ID;
		}
		
		switch ( $option ) {
			default:
				break;
			case 'full_name':
				$update_name = get_user_meta( $user_id, 'first_name', true ) . ' ' . get_user_meta( $user_id, 'last_name', true );
				break;
			case 'sur_name':
				$fname = get_user_meta( $user_id, 'first_name', true );
				$lname = get_user_meta( $user_id, 'last_name', true );
				$update_name = $lname . ' ' . $fname;
				break;
			case 'initial_name':
				$fname = get_user_meta( $user_id, 'first_name', true );
				$lname = get_user_meta( $user_id, 'last_name', true );
				$update_name = $fname . ' ' . $lname[0];
				break;
			case 'initial_name_f':
				$fname = get_user_meta( $user_id, 'first_name', true );
				$lname = get_user_meta( $user_id, 'last_name', true );
				$update_name = $fname[0] . ' ' . $lname;
				break;
			case 'nickname':
				$update_name = get_user_meta( $user_id, 'nickname', true );
				break;
		}

		if ( isset( $update_name ) ) {
			
			$arr_user =  array( 'ID' => $user_id, 'display_name' => $update_name );
			$return = wp_update_user( $arr_user );

			if( is_wp_error( $return ) ) {
				wp_die(  '<pre>' . var_export( array( 'message' => $return->get_error_message(), 'dump' => $arr_user, 'changes' => $changes ), true ) . '</pre>'  );
			}
			

		}

		if ( isset( $changes['first_name'] ) && isset( $changes['last_name'] ) ) {
			
			$full_name = $ultimatemember->user->profile['display_name'];
			$full_name = $ultimatemember->validation->safe_name_in_url( $full_name );

			update_user_meta( $ultimatemember->user->id, 'full_name', $full_name );

			
		}
		
		// regenerate slug
		$ultimatemember->permalinks->profile_url( true );



	}

add_action( 'profile_update', 'um_user_profile_update_cc', 10, 2);
	function um_user_profile_update_cc( $user_id ) {
		global $ultimatemember;
		$site_url = get_bloginfo('wpurl');
		$user_info = get_userdata( $user_id );
		$headers = 'From: ' .$user_info->user_login. '<' .$user_info->user_email. '>';

		if (isset($_POST['news_ellak_19'])) {
			$ccradio = $user_info->news_ellak_19;
			// THIS ARRAY CONTAINS THE INPUT FIELDS DATA
			//$data = 'username='.$user_info->user_login.'&email='.$user_info->user_email.'&password1=ccR@dio123&password2=ccR@dio123&tos=1';
			$datacc = array(
				'username' => $user_info->user_login,
				'email' => $user_info->user_email,
				'password1' => 'ccR@dio123',
				'password2' => 'ccR@dio123',
				'tos' => '1',
				'submit' => 'Εγγραφή'
			);
		}

			// START THE CURL PROCESS
			$chi = curl_init(); // initialize
			curl_setopt($chi, CURLOPT_URL, 'https://ccradio.ellak.gr/accounts/register/'); // form location url
			curl_setopt($chi, CURLOPT_POST, 1); // form method
			curl_setopt($chi, CURLOPT_POSTFIELDS, $datacc); // input fileds
			curl_setopt($chi, CURLOPT_RETURNTRANSFER, true); // get form result details
			$htmli = curl_exec($chi); // execute the curl process

			// DISPLAY FORM SUBMITTED RESULT
			//print_r($_POST);
			curl_close($chi);

			return $htmli;
	}

	add_action( 'profile_update', 'um_user_profile_update', 10, 2);
	function um_user_profile_update( $user_id ) {
		global $ultimatemember;
		$site_url = get_bloginfo('wpurl');
		$user_info = get_userdata( $user_id );
		$headers = 'From: ' .$user_info->user_login. '<' .$user_info->user_email. '>';

		$str = implode(', ', $user_info->user_lists_reg);
		if (isset($_POST['user_lists_reg'])) {
		//if (strlen($str)>1)
		if (strpos($str, 'Ανοιχτές Τεχνολογίες στην Εκπαίδευση (edu.ellak.gr)') !== FALSE) {
			$list_mail = "edu+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'Βήμα Δημόσιας Διαβούλευσης της ΕΕΛ/ΛΑΚ') !== FALSE) {
			$list_mail = "eellak-advisory+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'ΕΛ/ΛΑΚ και Επιχειρηματικότητα') !== FALSE) {
			$list_mail = "imeres-sinergasias+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'Επιχειρήσεις με R&D δραστηριότητα') !== FALSE) {
			$list_mail = "kainotomia+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'Προώθηση ανοικτού ελληνικού περιεχομένου (mycontent.ellak.gr)') !== FALSE) {
			$list_mail = "mycontent+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'open-source  (Λίστα γενικής ενημέρωσης για το ΕΛΛΑΚ στην Ελλάδα)') !== FALSE) {
			$list_mail = "open-source+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		// elseif (strpos($str, 'Προώθηση των ανοιχτών αδειών (creativecommons.gr)') !== FALSE) {
		// $list_mail = "";
		// }
		if (strpos($str, 'Ανοιχτή ασύρματη πρόσβαση (openwifi.gr)') !== FALSE) {
			$list_mail = "openwifi+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		// elseif (strpos($str, 'Εφαρμογή Οργανογράμματος') !== FALSE) {
		// $list_mail = "";
		// }
		if (strpos($str, 'Ενημέρωση για τις δράσεις της ΕΕΛ/ΛΑΚ') !== FALSE) {
			$list_mail = "press+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'SCRIPTUM: εφαρμογή Ανοιχτού Λογισμικού για τήρηση πρωτοκόλλου και ανάθεσης υποθέσεων') !== FALSE) {
			$list_mail = "scriptum-users+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'Λίστα Ενημέρωσης για τις Ανοιχτές Τεχνολογίες για τη Σύγχρονη Πόλη (Smart Cities)') !== FALSE) {
			$list_mail = "smartcities+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'Ομάδες Εργασίας ΕΕΛ/ΛΑΚ (Γενική λίστα ενημέρωσης)') !== FALSE) {
			$list_mail = "wg-members+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'Ομάδα Εργασίας: Ανοιχτό περιεχόμενο στην Εκπαίδευση') !== FALSE) {
			$list_mail = "wg-oer+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'Ομάδα Εργασίας: Ανοικτά Δεδομένα') !== FALSE) {
			$list_mail = "wg-opendata+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'Ομάδα Εργασίας: Ανοιχτή Διακυβέρνηση') !== FALSE) {
			$list_mail = "wg-opengov+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'Ομάδα Εργασίας: Ανοιχτό Hardware και ασύρματα δίκτυα') !== FALSE) {
			$list_mail = "wg-openhardware+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'Ομάδα εργασίας: Καινοτομία και Επιχειρηματικότητα') !== FALSE) {
			$list_mail = "wg-innovation+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'Ομάδα Εργασίας: Ανοιχτό Λογισμικό') !== FALSE) {
			$list_mail = "wg-foss+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'Ομάδα Εργασίας: Ανοιχτά Πρότυπα και άδειες') !== FALSE) {
			$list_mail = "wg-openstandards+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
		if (strpos($str, 'Ομάδα Εργασίας: Ασφάλεια Πληροφοριακών Συστημάτων και Προστασία Προσωπικών Δεδομένων') !== FALSE) {
			$list_mail = "privacy+subscribe@ellak.gr";
			$to = $list_mail;
			$subject = "New user " .$user_info->user_login . " ";
			$message = "E-mail: ".$user_info->user_email."\n\n ".$site_url."";
			wp_mail( $to, $subject, $message, $headers);
		}
	}
	if (isset($_POST['news_ellak_19_20'])) {
		$to = "info@eellak.gr";//get_option('admin_email');
		$subject = "Εγγραφή ιστότοπου " .$user_info->user_website. " στο planet.ellak.gr";
		$message = "E-mail: ".$user_info->user_email."\nΙστότοπος χρήστη: ".$user_info->user_website." - ".implode(', ', $user_info->news_ellak_19_20)."\n\n ".$site_url."";
		wp_mail( $to, $subject, $message, $headers);
	 }
	 if (isset($_POST['news_ellak_19_20']) || isset($_POST['user_lists_reg']) || isset($_POST['news_ellak_28']) || isset($_POST['news_ellak']) || isset($_POST['news_ellak_19'])) {
		$to = 'info@eellak.gr';
 		$subject = "Profile Updated " .$user_info->user_login . " - ".$site_url."";
 		$message = "E-mail: ".$user_info->user_email."\nΙστότοπος χρήστη: ".$user_info->user_website." - ".implode(', ', $user_info->news_ellak_19_20)."\nNewsletters: ".implode(', ', $user_info->news_ellak).", ".implode(', ', $user_info->news_ellak_28)."\nCCradio: ".implode(', ', $user_info->news_ellak_19)."\nΛίστες: ".implode(', ', $user_info->user_lists_reg)."\nΤομείς ενδιαφέροντος: ".implode(', ', $user_info->interests)."\n\n ".$site_url."";
 		wp_mail( $to, $subject, $message, $headers);
	}
	}

	add_action( 'profile_update', 'um_user_profile_update_nl', 10, 2);
	function um_user_profile_update_nl( $user_id ) {
		global $ultimatemember;
		$site_url = get_bloginfo('wpurl');
		$user_info = get_userdata( $user_id );
		$headers = 'From: ' .$user_info->user_login. '<' .$user_info->user_email. '>';
		if (isset($_POST['news_ellak_28']) || isset($_POST['news_ellak'])) {
			if (isset($_POST['news_ellak_28'])) {
				$data = array(
					'email' => $user_info->user_email,
					'list[5]' => '',
					'list[6]' => 'signup',
					'subscribe' => 'Γραφτείτε στα επιλεγμένα Newsletters'
				);
			}

			if (isset($_POST['news_ellak'])) {
				$data = array(
					'email' => $user_info->user_email,
					'list[5]' => 'signup',
					'list[6]' => '',
					'subscribe' => 'Γραφτείτε στα επιλεγμένα Newsletters'
				);
			}

			if (isset($_POST['news_ellak']) && isset($_POST['news_ellak_28'])) {
				$data = array(
					'email' => $user_info->user_email,
					'list[5]' => 'signup',
					'list[6]' => 'signup',
					'subscribe' => 'Γραφτείτε στα επιλεγμένα Newsletters'
				);
			}
		}

			// START THE CURL PROCESS
			$ch = curl_init(); // initialize
			curl_setopt($ch, CURLOPT_URL, 'https://newsletters.ellak.gr/?p=subscribe&id=1'); // form location url
			curl_setopt($ch, CURLOPT_POST, 1); // form method
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // input fileds
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // get form result details
			$html = curl_exec($ch); // execute the curl process
			curl_close($ch);
			return $html;
	}