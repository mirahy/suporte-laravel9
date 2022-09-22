<?php
require_once('config.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

//liberar requisições apenas apartir do sistema de suporte
$pegar_ip = $_SERVER["REMOTE_ADDR"];
if (getenv('BLOCK_HOST_SUPORTE') !== "*" && $pegar_ip != getenv('BLOCK_HOST_SUPORTE'))
	exit("Requisição Não permitida apartir desta origem!");
/*require_login();
if (!is_siteadmin()){ // Somente administrador do Moodle acessa essa página
	redirect("$CFG->wwwroot/");
	exit();
}*/

/// Start output log
$starttime = microtime();
$timenow = time();
echo("Server Time: ".date('r',$timenow)."<br><br>");



//-----------
$adminAcc = get_admin();
if (!$adminAcc) {
    mtrace("Error: Conta admin não encontrada");
    die;
}
$arquivoBackup = null;
$courseId = null;
if ( !isset ($_POST['modo']) || ($_POST['modo'] == 'cria' || $_POST['modo'] == 'full') ) {
    $arquivoBackup = obterArquivo($_FILES['backupfile']);
    if (!$arquivoBackup)
        exit();
	$courseImportId = isset ($_POST['courseImportId']) && $_POST['courseImportId'] ? $_POST['courseImportId'] : null;
	$categoryid = isset ($_POST['categoryid'])  && $_POST['categoryid'] ? $_POST['categoryid'] : null; 
	$courseId = null;
	if ($courseImportId) {
		$courseId = restauraBackup($arquivoBackup, $categoryid);
		$resFile = gerarBackup ($courseImportId);
		$courseId = restauraBackup($resFile, $categoryid, $courseId);
	}
	else {
		$courseId = restauraBackup($arquivoBackup, $categoryid);
	}
}
if (!isset ($_POST['modo']) || ($_POST['modo'] == 'insere' || $_POST['modo'] == 'cria' || $_POST['modo'] == 'full')) {
    $usuarios = obterListaUsuarios($_POST['usuarios']);
    if(!$courseId)
        $courseId = isset ($_POST['courseid']) ? $_POST['courseid'] : null;
    if ($usuarios)
        adicionarUsuarios($courseId, $usuarios, isset ($_POST['modo']) && $_POST['modo'] == 'cria');
    else 
        echo '<span style="color: red;">Sem usuários para adicionar!</span><br>';
}
if (isset ($_POST['modo']) && $_POST['modo'] == 'cadastra' ) {
    $usuarios = obterListaUsuarios($_POST['usuarios']);
    if(!$courseId)
        $courseId = isset ($_POST['courseid']) ? $_POST['courseid'] : null;
    $senhaPadrao = isset ($_POST['senhapadrao']) ? $_POST['senhapadrao'] : getenv('SENHA_PADRAO_CONTAS_MANUAIS');
    if ($usuarios)
        adicionarUsuariosApenas($usuarios,$courseId,$senhaPadrao, isset($_POST['desativaEstudantes']) ? (bool)$_POST['desativaEstudantes'] : false);
    else 
        echo '<span style="color: red;">Sem usuários para adicionar!</span><br>';
}

//-----------



$difftime = microtime_diff($starttime, microtime());
echo("<br><br>Server Time: ".date('r',$timenow));
echo("<br>Execution took ".$difftime." seconds");





//Prepara o arquivo de backup
function obterArquivo($file) {
	$UPLOAD_DIR = '/tmp/';
	$uploadfile = $UPLOAD_DIR . basename($file['name']);

	
	if (endsWith($uploadfile, '.mbz') && move_uploaded_file($file['tmp_name'], $uploadfile)) {
		echo "Arquivo válido e enviado com sucesso.<br>";
		return $uploadfile;
	} else {
		echo '<span style="color: red;">Falha no upload!</span><br>';
		return null;
	}
}

//obtem a lista de usuários apartir do parametro
function obterListaUsuarios($listusers) {
	return json_decode($listusers);
}

function restauraBackup($arquivoBackup, $idCategoria = null, $courseReplaceId = null) {
	global $DB;
	global $CFG;
	global $adminAcc;
	
	//diretório temporário de extração
	$rand = 2;
	while(strlen($rand)<10) { $rand = '0'.$rand; }
	$rand .= rand();
	
	check_dir_exists($CFG->dataroot . '/temp/backup');
	echo("Extraindo o Arquivo de Backup para: ".$CFG->dataroot . '/temp/backup/' . $rand . "<br>");
	$phar = new PharData($arquivoBackup);
    $phar->extractTo($CFG->dataroot . '/temp/backup/' . $rand);
	// Get or create category
	if (file_exists($CFG->dataroot . '/temp/backup/' . $rand . '/course/course.xml')) {
		$xml = simplexml_load_file($CFG->dataroot . '/temp/backup/' . $rand . '/course/course.xml');
		$shortname = (string)$xml->shortname;
		$fullname = (string)$xml->fullname;
		$categoryname = (string)$xml->category->name;
		$categoryid = $idCategoria;
		if (!$idCategoria) {
			$categoryid = $DB->get_field('course_categories', 'id', array('name'=>$categoryname));
			if (!$categoryid) {
				$categoryid = $DB->insert_record('course_categories', (object)array(
					'name' => $categoryname,
					'parent' => 0,
					'visible' => 1
				));
				$DB->set_field('course_categories', 'path', '/' . $categoryid, array('id'=>$categoryid));
			}
		}
		
		// Create new course
		$courseid = $courseReplaceId ?? restore_dbops::create_new_course($fullname, $shortname, $categoryid);
		// Restore backup into course
		$controller = new restore_controller($rand, $courseid,
				backup::INTERACTIVE_NO, backup::MODE_SAMESITE, $adminAcc->id,
				($courseReplaceId ? backup::TARGET_CURRENT_DELETING : backup::TARGET_NEW_COURSE));
		$controller->get_logger()->set_next(new output_indented_logger(backup::LOG_INFO, false, true));
		$controller->execute_precheck();
		if ($courseReplaceId)
			$controller->get_plan()->get_setting('keep_roles_and_enrolments')->set_value(true);
		$controller->execute_plan();
		echo("Curso Criado: [".$courseid."]<br><br>");
	} else {
		exit('Falha ao processar course.xml.<br><br>');
	}
	unset($xml,$shortname,$fullname,$categoryname,$categoryid,$controller,$nmuext,$rand);
	return $courseid;
}

function gerarBackup ($courseid) {
	global $DB;
	global $CFG;
	global $adminAcc;
	
	$dir = ($CFG->dataroot . '/temp/backup');
	if (!file_exists($dir) || !is_dir($dir) || !is_writable($dir)) {
		mtrace("Destination directory does not exists or not writable.");
		die;
	}
	if (!$courseid) {
		mtrace("Invalid courseid");
		die;
	}
	$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

	mtrace('Performing backup...');
	$bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
								backup::INTERACTIVE_YES, backup::MODE_GENERAL, $adminAcc->id);
	// Set the default filename.
	$format = $bc->get_format();
	$type = $bc->get_type();
	$id = $bc->get_id();
	$bc->get_plan()->get_setting('users')->set_value(false);
	$users = false;
	$anonymised = $bc->get_plan()->get_setting('anonymize')->get_value();
	$filename = backup_plan_dbops::get_default_backup_filename($format, $type, $id, $users, $anonymised);
	$bc->get_plan()->get_setting('filename')->set_value($filename);

	// Execution.
	$bc->finish_ui();
	$bc->execute_plan();
	$results = $bc->get_results();
	$file = $results['backup_destination']; // May be empty if file already moved to target location.

	// Do we need to store backup somewhere else?
	if (!empty($dir)) {
		if ($file) {
			mtrace("Writing " . $dir.'/'.$filename);
			if ($file->copy_content_to($dir.'/'.$filename)) {
				$file->delete();
				mtrace("Backup completed.");
			} else {
				mtrace("Destination directory does not exist or is not writable. Leaving the backup in the course backup file area.");
			}
		}
	} else {
		mtrace("Backup completed, the new file is listed in the backup area of the given course");
	}
	$bc->destroy();
	return $dir.'/'.$filename;
}

/**
 * Adiciona usuários a uma sala; criando o usuário caso ainda não exista
 * 
 * @param int $courseid id da sala
 * @param array $usuarios [0]username, [1]email, [2]fullname, [3] isprofessor
 *
 * @return void
 */
function adicionarUsuarios($courseid, $usuarios, $professorSomente = false, $desativaEstudantes = false) {
    $passs = password_hash (getenv('SENHA_PADRAO_CONTAS_MANUAIS'), PASSWORD_DEFAULT, array ('cost' => 4));
	$enrols = get_all_users_course($courseid);
	foreach ($usuarios as $usuario) {
		$log ='';
		$cpf = strtolower(trim($usuario[0]));

		//Checa se é Professor ou estudante e atribui o roleid
		$roleid = $usuario[3] ? 3 : 5; 
		if ($roleid == '5') {
			if ($professorSomente)
				continue;
			$rolename = 'estudante';
		}
		else 
			$rolename = 'professor';
		

		if ($usuario[0] === "username")//Ignora cabeçalho CSV
			continue;

		if (!$user = core_user::get_user_by_username($cpf)) {

			if (checaSeUserExisteLDAP($cpf)){
				create_user_moodle($usuario,'ldap','not cached');
			} else {
				create_user_moodle($usuario,'manual', $passs);
			}

			if (!$user = core_user::get_user_by_username($cpf)) {
				echo '<b>CPF não encontrado no Moodle: <span style="color: #ff0000;">'.trim($cpf).'</span></b><br><br>';
				continue;
			} 
			else 
				$log .= ' Usuário inserido no Moodle com sucesso ';
		}

		$log .= '<b>'.$cpf.'</b>: ';

		$enrol = get_enrolments($user->id,$courseid);
		if(!empty ($enrol)){
            $log .= ' Já matriculado, ';
			if ($enrol->status && $desativaEstudantes) {
				reativaEstudante($enrol->id);
				$log .= ' Reativado, ';
			}
			unset($enrols[$enrol->id]);
        } 
		else {
			if(set_enrolments($user->id,$courseid))//Matricula um aluno em um curso
				$log .= ' <span style="color: #088A08;">Matricula inserida com sucesso</span>, ';
			else 
				$log .= '<b><span style="color: #ff0000;"> - falta inserir matrícula, </span></b>';
        }

		if(!empty (get_assignments($user->id,$courseid,$roleid))){
			$log .= 'já está com papel de '.$rolename;
		} 
		else {
			if(set_assignments($user->id,$courseid,$roleid))//Atribui o papel(estudante:5 || professor: 3 ) na matricula de um curso
				$log .= ' <span style="color: #088A08;">Papel inserido com sucesso.</span>';
			else  
				$log .= '<b><span style="color: #ff0000;">falta inserir papel de '.$rolename.',</span></b>';
		}

		echo $log.'<br><br>';
	}
	if ($desativaEstudantes && count($enrols)) {
		echo desativaEstudantes($enrols);
	}
}

function adicionarUsuariosApenas($usuarios, $courseid, $senhaPadrao = '', $desativaEstudantes = false) {
    $passs = password_hash ($senhaPadrao, PASSWORD_DEFAULT, array ('cost' => 4));
    $enrols = get_all_users_course($courseid);
	foreach ($usuarios as $usuario) {
		$log ='';
		$entraNaConta = false;
		$cpf = strtolower(trim($usuario[0]));
		$nomeUsuario = $usuario[2];

		//Checa se é Professor ou estudante e atribui o roleid
		$roleid = $usuario[4] ? 3 : 5; 
		if ($roleid == '5')
			$rolename = 'estudante';
		else 
			$rolename = 'professor';


		if ($usuario[0] === "username")//Ignora cabeçalho CSV
			continue;

		if (!$user = core_user::get_user_by_username($cpf)) {

			if (checaSeUserExisteLDAP($cpf)){
				create_user_moodle($usuario,'ldap','not cached');
			} else {
				create_user_moodle($usuario,'manual', $senhaPadrao ? $passs : password_hash ($usuario[3], PASSWORD_DEFAULT, array ('cost' => 4)) );
			}

			if (!$user = core_user::get_user_by_username($cpf)) {
				echo '<b>CPF não encontrado no Moodle: <span style="color: #ff0000;">'.trim($cpf).'</span></b><br><br>';
				continue;
			} 
			else 
				$log .= ' Usuário inserido no Moodle com sucesso ';
		}

		$log .= '<b>'.$cpf.' - '.$nomeUsuario.'</b>: ';

		if ($courseid) {
			$enrol = get_enrolments($user->id,$courseid);
			if(!empty ($enrol)){
				$log .= ' Já matriculado, ';
				if ($enrol->status && $desativaEstudantes) {
					reativaEstudante($enrol->id);
					$log .= ' Reativado, ';
					$entraNaConta = true;
				}
				unset($enrols[$enrol->id]);
			} 
			else {
				$entraNaConta = true;
				if(set_enrolments($user->id,$courseid))//Matricula um aluno em um curso
					$log .= ' <span style="color: #088A08;">Matricula inserida com sucesso</span>, ';
				else 
					$log .= '<b><span style="color: #ff0000;"> - falta inserir matrícula, </span></b>';
			}

			if(!empty (get_assignments($user->id,$courseid,$roleid))){
				$log .= 'já está com papel de '.$rolename;
			} 
			else {
				$entraNaConta = true;
				if(set_assignments($user->id,$courseid,$roleid))//Atribui o papel(estudante:5 || professor: 3 ) na matricula de um curso
					$log .= ' <span style="color: #088A08;">Papel inserido com sucesso.</span>';
				else  
					$log .= '<b><span style="color: #ff0000;">falta inserir papel de '.$rolename.',</span></b>';
			}
		}
		

		if (!$desativaEstudantes || $entraNaConta)
			echo $log.'<br><br>';
	}
	if ($desativaEstudantes && count($enrols)) {
		echo desativaEstudantes($enrols);
	}
}

/**
 * Cria um usuário no Moodle 
 * OBS:  $userObject->auth = 'ldap'; // Tipo de autenticação: 'ldap' ou 'manual';
 * OBS2: $userObject->password = 'not cached'; // Se o tipo de auth for LDAP use: 'not cached', se for manual tem que gerar senha;
 * 
 *
 * @param array $usuario [0]username, [1]email, [2]fullname
 *
 * @return bool true if user created, else false.
 */

function create_user_moodle($usuario,$auth,$password){
    global $DB;

    $full_name  = tratar_nome ($usuario[2]);
    $first_name = strtok ($full_name, ' ');
    $last_name  = str_replace ($first_name . ' ', '', $full_name);
   
    echo 'firstname: <span style="color: #ff6127;">'.$first_name.'</span> lastname: <span style="color: #ff9327;">'.$last_name.'</span> - mail:<span style="color: #2761ff;">'.$usuario[1].'</span> - ';

    $userObject = new stdClass(); 

    $userObject->username   = strtolower(trim($usuario[0]));
    $userObject->firstname  = $first_name;
    $userObject->lastname   = $last_name;
    $userObject->email      = $usuario[1];  

    #Predefinições
    $userObject->auth = $auth;//'manual'; //'ldap' || 'manual';
    $userObject->confirmed = 1;
    $userObject->policyagreed = 0;
    $userObject->deleted = 0;
    $userObject->suspended = 0;
    $userObject->mnethostid = 1;
    $userObject->password = $password;//'$2y$10$mXHfXDOhsL.pq92HHpZ6BelwaAW8V6LoHurLjXSpYD0a9PrAuRjaK';//'not cached'(se LDAP) || gerar senha (se manual);
    $userObject->idnumber = '';
    $userObject->emailstop = 0;
    $userObject->icq = '';
    $userObject->skype = '';
    $userObject->yahoo = '';
    $userObject->aim = '';
    $userObject->msn = '';
    $userObject->phone1 = '';
    $userObject->phone2 = '';
    $userObject->institution = '';
    $userObject->department = '';
    $userObject->address = '';
    $userObject->city = '';
    $userObject->country = '';
    $userObject->lang = 'pt_br';
    $userObject->calendartype = 'gregorian';
    $userObject->theme = '';
    $userObject->timezone = '99';
    $userObject->firstaccess = 0;
    $userObject->lastaccess = 0;
    $userObject->lastlogin = 0;
    $userObject->currentlogin = 0;
    $userObject->lastip = '';
    $userObject->secret = '';
    $userObject->picture = 0;
    $userObject->url = '';
    $userObject->description = '';
    $userObject->descriptionformat = 1;
    $userObject->mailformat = 1;
    $userObject->maildigest = 0;
    $userObject->maildisplay = 2;
    $userObject->autosubscribe = 1;
    $userObject->trackforums = 0;
    $userObject->timecreated = time();
    $userObject->timemodified = time();
    $userObject->trustbitmask = 0;
    $userObject->imagealt = NULL;
    $userObject->lastnamephonetic = NULL;
    $userObject->firstnamephonetic = NULL;
    $userObject->middlename = NULL;
    $userObject->alternatename = NULL;

        if($DB->insert_record('user', $userObject))
          return true;
    

}

function checaSeUserExisteLDAP($chave) {
	$ldapserver = getenv('ADLDAP_CONTROLLERS');
	$ldapuser   = getenv('ADLDAP_ADMIN_USERNAME'); //usuário de conexão no servidor ldap
	$ldappass   = getenv('ADLDAP_ADMIN_PASSWORD'); // senha deste usuário
	$ldaptree   = getenv('ADLDAP_BASEDN');

	//abrir uma conexão
	$ldapconn = ldap_connect($ldapserver); //or die("Não é possível conectar ao Servidor LDAP.");

	if($ldapconn) {
		ldap_set_option ($ldapconn, LDAP_OPT_REFERRALS, 0);
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		// bind no servidor
		$ldapbind = ldap_bind($ldapconn, $ldapuser, $ldappass) or die ("Erro no bind: ".ldap_error($ldapconn));
		if ($ldapbind) {
			$samaccountname = $chave;
			$filter="(samaccountname=$samaccountname)";

			$res = ldap_search($ldapconn, $ldaptree, $filter);
			$first = ldap_first_entry($ldapconn, $res);
			if ($first){
				return true;
				//$data = ldap_get_dn($ldapconn, $first);
			}
		}
    }
    echo "Não é possível conectar ao Servidor LDAP.";
	return false;
}

/**
 * Insere uma matrícula em um curso
 * 
 * @param int  $userid 
 * @param int  $courseid
 *
 * @return bool true if user enrolments created, else false.
 */
function set_enrolments($userid,$courseid){
    global $DB;

    $enrolid = get_enrol($courseid);

    $userEnrolmentsObject = new stdClass();
    $userEnrolmentsObject->status = 0;
    $userEnrolmentsObject->enrolid = $enrolid;
    $userEnrolmentsObject->userid = $userid;
    $userEnrolmentsObject->timestart =  time();  
    $userEnrolmentsObject->timeend = 0; 
    $userEnrolmentsObject->modifierid =  0; 
    $userEnrolmentsObject->timecreated =  time(); 
    $userEnrolmentsObject->timemodified = time();

    if($DB->insert_record('user_enrolments', $userEnrolmentsObject))
        return true;
     
}

/**
 * Insere um papel de usuário em um curso
 * 
 * @param int $userid 
 * @param int $courseid 
 * @param int $roleid papel dentro de um curso: estudante: 5; professor: 3; página para consultar os ids: @dominio@/admin/roles/manage.php ou no bd "mdl_role"
 *
 * @return bool true if role assignments created, else false.
 */
function set_assignments($userid,$courseid,$roleid){
    global $DB;

    $contextid = get_context($courseid);

    $roleAssignmentObject = new stdClass();
    $roleAssignmentObject->roleid = $roleid;
    $roleAssignmentObject->contextid = $contextid;
    $roleAssignmentObject->userid = $userid;
    $roleAssignmentObject->timemodified =  time();  
    $roleAssignmentObject->modifierid = 0; 
    $roleAssignmentObject->component =  '';
    $roleAssignmentObject->itemid =  0; 
    $roleAssignmentObject->sortorder = 0;

    if($DB->insert_record('role_assignments', $roleAssignmentObject))
        return true;
    
}

/**
 * Verifica se existe matrícula de um usuário em um curso
 * 
 * @param int $userid
 * @param int $courseid
 *
 * @return int|bool returns userid if existing, else false.
 */
function get_enrolments($userid,$courseid){
    global $DB;

    $enrolid = get_enrol($courseid);

    $sql = " SELECT  e.id, e.status
               FROM {user_enrolments} e 
              WHERE e.enrolid=? AND  e.userid=?";
    $enrolments = $DB->get_record_sql($sql, array($enrolid, $userid));

    return $enrolments;
    
}

/**
 * Verifica se existe um papel de um usuário em um curso
 * 
 * @param int $userid
 * @param int $courseid
 *
 * @return int|bool returns userid if existing, else false.
 */
function get_assignments($userid,$courseid,$roleid){
    global $DB;

    $contextid = get_context($courseid);

    $sql = "SELECT  r.id
              FROM {role_assignments} r 
             WHERE r.contextid=? AND  r.userid=? AND r.roleid=?";
    $assignments = $DB->get_record_sql($sql, array($contextid, $userid, $roleid));

    if($assignments)
      return $userid;
    
}

/**
 * Procura um contexto de um determinado curso 
 *   Para entender melhor os contextos no Moodle: ginux.online/quais-sao-os-niveis-de-contexto-no-moodle/
 * 
 * @param int $courseid
 *
 * @return int|bool returns contextid if existing, else false.
 */
function get_context($courseid){
    global $DB;

    $sqlcontext = "SELECT  c.id
                    FROM {context} c 
                   WHERE c.contextlevel=50 AND c.instanceid=?";
    $context = $DB->get_record_sql($sqlcontext, array($courseid));

    if($context)
      return $context->id;
    
}

/**
 * Busca id da tabela do metodo de inscrição por padrão estou usando metodo de inscrição manual,
 *  pois já existe em todos os cursos, 
 *  IMPORTANTE: senão eu teria que criar um metodo em cada curso.(que pode ser importante tbm,
 *  pois daria para diferenciar usuarios inseridos por esse sistema e usuarios inseridos por nós de forma manual,
 *  e permitira ocultar e excluir todo o metodo de inscrição com os alunos, caso fosse inscrito em um curso errado) 
 * 
 * @param int $courseid 
 *
 * @return int|bool returns enrolid if existing, else false.
 */
function get_enrol($courseid){
    global $DB;

    $sqlenrolid = " SELECT  e.id
                      FROM {enrol} e 
                    WHERE e.enrol='manual' AND  e.courseid=?";
    $enrol = $DB->get_record_sql($sqlenrolid, array($courseid));

    if($enrol)
      return $enrol->id;
    
}

/**
 * Busca um usuário no bd do Moodle
 * 
 * @param varchar(100) $username
 *
 * @return stdClass|bool returns user if existing, else false.
 */
function get_user($username){
    global $DB;
    $user = $DB->get_record('user', array('username' => trim($username)));

    if($user)
      return $user;
    
}

function get_all_users_course($courseid) {
	global $DB;

    $enrolid = get_enrol($courseid);
	$contextid = get_context($courseid);

    $sql = "SELECT e.id, e.userid, r.roleid, u.username, e.status, u.firstname, u.lastname
		FROM {user_enrolments} e 
		JOIN {role_assignments} r ON e.userid = r.userid
		JOIN {user} u ON e.userid = u.id
		WHERE r.contextid=? AND e.enrolid=?";
    return $DB->get_records_sql($sql, array($contextid, $enrolid));
}

function desativaEstudantes($enrols) {
	global $DB;
	$ret = "";
	foreach ($enrols as $e) {
		if ($e->roleid == 5 && !$e->status) {
			$obj = [
				'id' => $e->id,
				'status' => 1
			];
			$ret .= "<br><b>". $e->username . " - ". $e->firstname . " ". $e->lastname . "</b>";
			$DB->update_record("user_enrolments", $obj);
		}
	}
	return $ret ? '<span style="color: #ff0000;">Desativando estudantes:</span>' . $ret . "<br>" : "";
}

function reativaEstudante($enrolid) {
	global $DB;
	
	$obj = [
		'id' => $enrolid,
		'status' => 0
	];
	$DB->update_record("user_enrolments", $obj);
}

function tratar_nome ($nome) {
  $saida = "";

  $nome = mb_strtolower($nome, 'UTF-8'); // Converter o nome todo para minúsculo
  $nome = explode(" ", $nome); // Separa o nome por espaços

  for ($i=0; $i < count($nome); $i++) {
    $array_de_tratamento = ["da", "e", "da", "dos", "do", "das"];
    if (in_array($nome[$i], $array_de_tratamento)) {
      $saida .= $nome[$i].' '; // Se a palavra estiver dentro das complementares mostrar toda em minúsculo
    }else {
      $saida .=  mb_convert_case($nome[$i], MB_CASE_TITLE, "UTF-8").' '; // Se for um nome, mostrar a primeira letra maiúscula
    }
  }
  return trim($saida);
}

function endsWith($haystack, $needle) {
    return substr_compare($haystack, $needle, -strlen($needle)) === 0;
}

?>
