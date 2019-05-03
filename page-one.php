<?php
    // echo '<pre>';
    // print_r($_FILES['file']['name']);
    // echo '</pre>';

	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );    

    $month = array("1"=>"Січень","2"=>"Лютий","3"=>"Березень","4"=>"Квітень","5"=>"Травень", "6"=>"Червень", "7"=>"Липень","8"=>"Серпень","9"=>"Вересень","10"=>"Жовтень","11"=>"Листопад","12"=>"Грудень");


    function datf($di) {
        $source = $di;
        $date = new DateTime($source);
        return $date->format('d.m.Y'); // 31.07.2012
    }

    if (isset($_GET['id'])) {
        $idMaino = $_GET['id'];
    }


    if (isset($_POST['idpropert']) && isset($_POST['idmaino'])) {
        $idMaino = $_POST['idmaino'];
    }

    if (isset($_POST['copypropo']) && isset($_POST['idmaino'])) {
        $idMaino = $_POST['idmaino'];
        $Maino   = $wpdb->get_row("SELECT * FROM `maino`    WHERE `id` = ".$idMaino);
        $reestr  = $wpdb->get_row("SELECT * FROM `reestr_a` WHERE `id` = ".$Maino->reestr_id);
        if (empty($reestr)) {
            $reestr  = $wpdb->get_row("SELECT * FROM `reestr` WHERE `id` = ".$Maino->reestr_id);
        }
        $Propert = $wpdb->get_row("SELECT * FROM `property` WHERE `maino_id` = ".$idMaino." AND `reestr_id` = ".$Maino->reestr_id);
        $fildsn = json_decode($Propert->fields);
        $fildsn->nomber = ((int)$fildsn->nomber)+1;
        // print_r($Maino);
        $fieldd = array(
            'nomber' => $reestr->nomber.'-'.$fildsn->nomber,
            'reestr_id' => $Maino->reestr_id,
            'vid_id' => $Maino->vid_id,
            'opis' => $Maino->opis,
            'count' => $Maino->count, 
            'price' => $Maino->price, 
            'vartist' => $Maino->vartist, 
            'vikon' => $Maino->vikon,
            'status' => $Maino->status,
            'podpisant' => $Maino->podpisant,
            'datework' => $Maino->datework,
            'nom_d' => $reestr->nomber,
            'nom_o' => $fildsn->nomber
        );
        $wpdb->insert('maino', $fieldd);

        $idMaino = $wpdb->insert_id;

        $fieldd = array(
            'maino_id' => $idMaino,
            'reestr_id' => $Maino->reestr_id,
            'analogi' => $Propert->analogi,
            'fields' => json_encode($fildsn),
            'analizrinky' => $Propert->analizrinky, 
            'dolar_procent' => $Propert->dolar_procent, 
            'raschet' => $Propert->raschet, 
            'foto_map' => str_replace('https://nazaret.developsite.xyz', 'https://crm.nazaret-ltd.com.ua', str_replace('http://','https://',$Propert->foto_map)),
            'foto_src' => $Propert->foto_src,
            'tehstroika' => $Propert->tehstroika
        );
        $wpdb->insert('property', $fieldd);

    }


    if (isset($_POST['copyfromold']) && isset($_POST['idmaino'])) {
        $idMaino    = $_POST['idmaino'];
        $oldIdMaino = $_POST['oldzvit']; 
        $Maino      = $wpdb->get_row("SELECT * FROM `maino`    WHERE `id` = ".$idMaino);
        $reestr     = $wpdb->get_row("SELECT * FROM `reestr_a` WHERE `id` = ".$Maino->reestr_id);
        if (empty($reestr)) {
            $reestr = $wpdb->get_row("SELECT * FROM `reestr`   WHERE `id` = ".$Maino->reestr_id);
        }

        $oldMaino   = $wpdb->get_row("SELECT * FROM `maino`    WHERE `nomber` = '".$reestr->prev_id.'-'.$oldIdMaino."'");
        $oldPropert = $wpdb->get_row("SELECT * FROM `property` WHERE `maino_id` = ".$oldMaino->id." AND `reestr_id` = ".$oldMaino->reestr_id);
        $Propert    = $wpdb->get_row("SELECT * FROM `property` WHERE `maino_id` = ".$Maino->id." AND `reestr_id` = ".$Maino->reestr_id);

        $fildso = json_decode($oldPropert->fields);
        $fildsn = json_decode($Propert->fields);
        $fildso->nomber  = $fildsn->nomber;
        $fildso->dogovor = $fildsn->dogovor;
        $fildso->oldnomber = $_POST['oldzvit'];
        $wpdb->update( "property",
            array( "fields" => json_encode($fildso), 
                   "analogi" => $oldPropert->analogi, 
                   "analizrinky" => $oldPropert->analizrinky, 
                   "tehstroika" => $oldPropert->tehstroika, 
                   "foto_src" => $oldPropert->foto_src, 
                   "foto_map"    => str_replace('https://nazaret.developsite.xyz', 'https://crm.nazaret-ltd.com.ua', str_replace('http://','https://', $oldPropert->foto_map)), 
                   "dolar_procent" => $oldPropert->dolar_procent, 
                   "raschet" => $oldPropert->raschet ),
            array( "maino_id" => $idMaino, "reestr_id" => $Maino->reestr_id),
            array( '%s', '%s', '%s', '%s', '%s' ),
            array( '%d', '%d' )
        );
        echo '<script>window.location = "/property-rights-apartment/?id='.$Maino->id.'"</script>';

    }

    $foto_map = '';


    global $wp;
    global $wpdb;
    $current_url = home_url( add_query_arg( array(), $wp->request ) );
    $Maino   = $wpdb->get_row("SELECT * FROM `maino`    WHERE `id` = ".$idMaino);
    $reestr  = $wpdb->get_row("SELECT * FROM `reestr_a` WHERE `id` = ".$Maino->reestr_id);
    if (empty($reestr)) {
        $reestr  = $wpdb->get_row("SELECT * FROM `reestr` WHERE `id` = ".$Maino->reestr_id);
    }

    $client  = $wpdb->get_row("SELECT * FROM `s_client` WHERE `id` = ".$reestr->client_id);
    $firma   = $wpdb->get_row("SELECT * FROM `s_firma`  WHERE `id` = ".$reestr->firma_id);
    $Propert = $wpdb->get_row("SELECT * FROM `property` WHERE `maino_id` = ".$idMaino." AND `reestr_id` = ".$Maino->reestr_id);

    
    if (isset($_FILES['file']) && $_FILES['file']['name'] != '') {

        $uploadedfile = $_FILES['file'];

        $upload_overrides = array( 'test_form' => false );

        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
        
        if ( $movefile && ! isset( $movefile['error'] ) ) {
            // echo "File is valid, and was successfully uploaded.\n";
            // var_dump( $movefile );
        } else {
            /**
             * Error generated by _wp_handle_upload()
             * @see _wp_handle_upload() in wp-admin/includes/file.php
             */
            // echo $movefile['error'];
        }

        $foto_map = $movefile['url'];
        $foto_src = $movefile['file'];
    }

    if (isset($_FILES['files']) && !empty($_FILES['files'])) {
        $root = $_SERVER['DOCUMENT_ROOT'];
    
        $reestr_id = $reestr->id;
        // $idMaino = $Maino->id;
        if (isset($_POST['files_type'])) {
            $type = $_POST['files_type'];
        } else {
            $type = 'a';
        }
        
    
        if (!file_exists($root.'/uploads/'.$reestr_id.'/'.$idMaino)) {
            mkdir($root.'/uploads/'.$reestr_id.'/'.$idMaino, 0777, true);
        }
        $yes_files = [];
    
        $no_files = count($_FILES["files"]['name']);
        for ($i = 0; $i < $no_files; $i++) {
            if ($_FILES["files"]["error"][$i] > 0) {
                    // echo "Error: " . $_FILES["files"]["error"][$i] . "<br>";
            	echo '';
            } else {
                if (file_exists($root.'/uploads/'.$reestr_id.'/'.$idMaino.'/' . $_FILES["files"]["name"][$i])) {
                        echo 'File already exists : '.$root.'/uploads/'.$reestr_id.'/'.$idMaino.'/' . $_FILES["files"]["name"][$i];
                } else {
                    move_uploaded_file($_FILES["files"]["tmp_name"][$i], $root.'/uploads/'.$reestr_id.'/'.$idMaino.'/' . $_FILES["files"]["name"][$i]);
                        echo 'File successfully uploaded : '.$root.'/uploads/'.$reestr_id.'/'.$idMaino.'/' . $_FILES["files"]["name"][$i] . ' ';
                    $f = [];
                    $f['name'] =  $_FILES["files"]["name"][$i];
                    $f['link'] =  '/uploads/'.$reestr_id.'/'.$idMaino.'/' . $_FILES["files"]["name"][$i];
                    $yes_files[] = $f;
                }
            }
        }

        foreach ($yes_files as $file) {
            $id = $wpdb->insert( 'maino_file',array('reestr_id' => $reestr_id, 'maino_id' => $idMaino, 'type' => $type, 'file_pach' => $file['link'], 'name' => $file['name']));
        }
    }

    if (empty($Propert)) {
        $wpdb->insert( "property",
            array( "reestr_id" => $Maino->reestr_id, "maino_id" =>  $Maino->id ),
            array('%d','%d')
        );
    }
    if (isset($_POST['savepropert'])) {
        $wpdb->show_errors();

        $fieldi = $_POST['field'];
        $field = json_encode($_POST['field']);
        $analog = json_encode($_POST['analogi']);
        $analizrinky= json_encode($_POST['analizrinky']);
        $dolar_procent= json_encode($_POST['dolar_procent']);
        $raschet= json_encode($_POST['raschet']);
        $foto_map = str_replace('https://nazaret.developsite.xyz', 'https://crm.nazaret-ltd.com.ua', str_replace('http://','https://',$foto_map));
        $tehstroika = json_encode($_POST['tehstroika']);

        $nomn = explode("-", $fieldi['nomber']);

        $fulNomber = $fieldi['dogovor'].'-'.$fieldi['nomber'];
        // if ($fulNomber != $Maino->nomber) {
        // if ($Maino->nom_o != end($nomn)) {
        //     $wpdb->update( "maino",
        //         array( "nomber" => $fulNomber, "nom_d" => $reestr->nomber,  "nom_o" => end($nomn)),
        //         array( "id" => $Maino->id),
        //         array( '%s', '%s', '%d'),
        //         array( '%d')
        //     );
        // }

        if ($Maino->status == 'n') {
            $wpdb->update( "maino",
                array( "status" => 'w'),
                array( "id" => $Maino->id),
                array( '%s'),
                array( '%d')
            );
        }        

        $wpdb->update( "maino",
            array( "datework" => $_POST['datework'], "nomber" => $fieldi['nomber'], "nom_d" => $reestr->nomber,  "nom_o" => end($nomn)),
            array( "id" => $Maino->id),
            array( '%s', '%s', '%s', '%d'),
            array( '%d')
        );
        
        $wpdb->update( "property",
            array( "fields" => $field, "analogi" => $analog, "analizrinky" => $analizrinky, "dolar_procent" => $dolar_procent, "raschet" => $raschet ),
            array( "maino_id" => $idMaino, "reestr_id" => $Maino->reestr_id),
            array( '%s', '%s' ),
            array( '%d', '%d' )
        );

        $wpdb->update( "property",
            array( "tehstroika" => $tehstroika ),
            array( "maino_id" => $idMaino, "reestr_id" => $Maino->reestr_id),
            array( '%s', '%s' ),
            array( '%d', '%d' )
        );        

        if ($foto_map != '') {
            $wpdb->update( "property",
                array( "foto_map" => $foto_map, "foto_src" => $foto_src ),
                array( "maino_id" => $idMaino, "reestr_id" => $Maino->reestr_id),
                array( '%s', '%s' ),
                array( '%d', '%d' )
            );        
        }
        echo '<script>window.location = "'.$current_url.'/?id='.$Maino->id.'"</script>';
    }
    $Propert = $wpdb->get_row("SELECT * FROM `property` WHERE `maino_id` = ".$idMaino." AND `reestr_id` = ".$Maino->reestr_id);
    $fields  = json_decode($Propert->fields);
    $analogi = json_decode($Propert->analogi, true);
    $analizrinky = json_decode($Propert->analizrinky, true);
    $tehstroika = json_decode($Propert->tehstroika, true);
    $dolar_procent = json_decode($Propert->dolar_procent, true);
    $raschet = json_decode($Propert->raschet, true);
    $foto_map = str_replace('https://nazaret.developsite.xyz', 'https://crm.nazaret-ltd.com.ua', str_replace('http://','https://',$Propert->foto_map));

    $dt = explode('.',$Maino->datework);
    $date_today_year = (int)$dt[2];
    $date_today_month = (int)$dt[1];
    $month_array = array();
    // $month_array[] = $month[$date_today_month].' '.$date_today_year;
    for ($i=0; $i < 12; $i++) { 
        $date_today_month--;
        if ($date_today_month == 0) {
            $date_today_year--;
            $date_today_month = 12;  
        }
        $month_array[] = $month[$date_today_month].' '.$date_today_year;
    }
    // print_r($month_array);
    $month_array = array_reverse($month_array);

    $tehstroika = str_replace('\\','',$tehstroika);

    $analizrinky = str_replace('\\','',$analizrinky);
    $analizrinky = str_replace('&quot;','',$analizrinky);
    $analizrinky = str_replace('&ndash;','-',$analizrinky);
    $analizrinky = str_replace('&mdash;','-',$analizrinky);
    $analizrinky = str_replace('&rsquo;','"',$analizrinky);
    $analizrinky = str_replace('<p>&nbsp;</p>','',$analizrinky);
    $analizrinky = str_replace('&nbsp;',' ',$analizrinky);
    $analizrinky = str_replace('<br />','<br>',$analizrinky);
    $analizrinky = str_replace('</strong><strong>','',$analizrinky);
    $analizrinky = str_replace('strong','b',$analizrinky);
    $analizrinky = str_replace('&ge;','>=',$analizrinky);
    $analizrinky = str_replace('&laquo;','<<',$analizrinky);
    $analizrinky = str_replace('&raquo;','>>',$analizrinky);
    $analizrinky = str_replace('&plusmn;','+/-',$analizrinky);
    $analizrinky = str_replace('&#39;','"',$analizrinky);
    $analizrinky = str_replace('<a','<span',$analizrinky);
    $analizrinky = str_replace('</a','</span',$analizrinky);

    $dtt = explode('.',$Maino->datework);
    $dttf = $dtt[2].'-'.$dtt[1].'-'.$dtt[0];

    $sqlVal = "SELECT * FROM `s_valute` WHERE `currency`='USD' AND `date`='".$dttf."'";
    $valCurUSD = $wpdb->get_row($sqlVal);
    $sqlVal = "SELECT * FROM `s_valute` WHERE `currency`='EUR' AND `date`='".$dttf."'";
    $valCurEUR = $wpdb->get_row($sqlVal);
    $sqlVal = "SELECT * FROM `s_valute` WHERE `currency`='RUB' AND `date`='".$dttf."'";
    $valCurRUB = $wpdb->get_row($sqlVal);

    $mainoFile = $wpdb->get_results("SELECT * FROM `maino_file` WHERE `maino_id` = ".$idMaino." ORDER BY `type`");
    
    // echo '<pre style="font-size:10px;">';
    // foreach ($dolar_procent as $key => $value) {
    //     foreach ($value as $key_i => $value_i) {
            
    //     }
    // }
    // echo '</pre>';
?>

    <div class="row">
        <div class='col'>
            <div class="input-group input-group-sm mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text">№ договору</span>
                </div>
                <input type="text" class="form-control" readonly value="<?=$reestr->nomber;?>" >
            </div>
        </div>     
        <div class="col">
            <div class="input-group input-group-sm mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text">№ звiту</span>
                </div>
                <input type="text" class="form-control form-control-sm js-field-nomber-edit" name="field[nomber]" value="<?=($fields->nomber != '' ? $fields->nomber : str_replace($reestr->nomber.'-','', $Maino->nomber));?>">
            </div>            
        </div>           
        <div class='col'>
            <div class="input-group input-group-sm mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text">старий № договору</span>
                </div>
                <input type="text" class="form-control" readonly value="<?=$reestr->prev_id;?>" >
            </div>
        </div>        

        <div class="col">
           <form class="input-group input-group-sm mb-2" action="<?=$current_url;?>" method="POST">
                    <input type="hidden" name="copyfromold">
                    <input type="hidden" name="idmaino" value="<?=$idMaino;?>" >
                <div class="input-group-prepend">
                    <span class="input-group-text">№ звiту орігіналу</span>
                </div>
                    <input type="text" class="form-control js-oldzvit" name="oldzvit" value="<?=$fields->oldnomber?>" >
                <div class="input-group-prepend">
                    <input type="submit" value="Заповнити данні"  class="btn btn-success btn-sm w-100">
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class='col'>
            <div class="input-group input-group-sm mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text">Дата договору</span>
                </div>
                <input type="text" class="form-control" readonly name="date" value="<?=datf($reestr->date);?>" >
            </div>
        </div>
        <div class='col'>
            <div class="input-group input-group-sm mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text">Дата звiту</span>
                </div>
                <input type="text" class="form-control js-date" data-id="#datework"  value="<?=$Maino->datework;?>" >
            </div>
        </div>
        <div class='col'>
            <div class="input-group input-group-sm mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text">Дата завершення</span>
                </div>
                <input type="text" class="form-control js-date" data-id="#dateworkend"  value="<?=$fields->dateworkend;?>" >
            </div>
        </div>        
        <div class="col">
            <div class="input-group input-group-sm mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text">Термін дії звіту</span>
                </div>
                <select class="form-control form-control-sm js-termin">
                    <option <?php if ($fields->termindii == '1 місяць') { echo 'selected'; } ?> value="1 місяць">1 місяць</option>
                    <option <?php if ($fields->termindii == '3 місяця') { echo 'selected'; } ?> value="3 місяця">3 місяця</option>
                    <option <?php if ($fields->termindii == '6 місяців') { echo 'selected'; } ?> value="6 місяців">6 місяців</option>
                    <option <?php if ($fields->termindii == '12 місяців') { echo 'selected'; } ?> value="12 місяців">12 місяців</option>
                </select>
            </div>
        </div>        
    </div>
    <div class='row'>
        <div class='col'>
            <div class="input-group input-group-sm mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text">Замовник</span>
                </div>
                <input type="text" class="form-control" readonly name="client" value='<?=$client->pravforma." ".$client->name;?>' >
            </div>
        </div>
        <div class='col'>
            <div class="input-group input-group-sm mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text">Фірма-виконавець</span>
                </div>
                <input type="text" class="form-control js-auto-firma" readonly name="firma" value='<?=$firma->name;?>' >
            </div>
        </div>
    </div>
    <div class='row mb-3'>
        <div class='col-6'>
            <a href="/api/property-rights-apartment/doc_create.php?id=<?=$Propert->id;?>" target="_blank" class="btn btn-success btn-sm btn-primary js-recenzij-create-word w-100" type="button">сформувати WORD файл</a>
        </div>
        <div class='col-6'>
            <form action="<?=$current_url;?>" method="POST" >
                <input type="hidden" name="copypropo">
                <input type="hidden" name="idmaino" value="<?=$idMaino;?>" >
                <input type="submit" value="Скопировать"  class="btn btn-success btn-sm w-100">
            </form>
        </div>
    </div>
    <?php
    	$mainofull = $wpdb->get_results("SELECT *, `property`.`maino_id` as pid FROM `maino` LEFT JOIN `property` ON `property`.`maino_id` = `maino`.`id` WHERE NOT `maino`.`status` = 'd' AND `maino`.`vid_id` = 15 ORDER BY `nom_d`, `nom_o`");
    	// print_r($mainofull);
    ?>
    <div class="row mb-1">
        <div class='col-md'>
            <div class="input-group input-group-sm mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text">Скопіювати данні (Аналіз, Аналогі, Коефіцієнти)</span>
                </div>
                <select class="js-copy-mainoid-from form-control form-control-sm" na >
                	<option value="0">Вибрати</option>
                	<?php
                		foreach ($mainofull as $key => $value) {
						    $fieldsf  = json_decode($value->fields);
						    $analogif = json_decode($value->analogi);
                			?><option <?=($value->pid == $fields->mainofrom) ? 'selected' : '' ?> value="<?=$value->pid?>"><?=$value->nom_d.'-'.$fieldsf->nomber.' '.str_replace('\\', '', $fieldsf->pib)?></option><?php
                		}
                	?>
                </select>
            </div>
        </div>        
        <div class="col-md-3">
        	<input type="hidden" class="js-copy-mainoid" value="<?=$idMaino;?>" >
        	<button type="button" class="btn btn-success btn-sm w-100 js-copy-maino-btn">Заповнити данні</button>
        </div>
    </div>
    <div class="row mb-5">
        <div class='col-md'>
            <div class="input-group input-group-sm mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text">Скопіювати данні (ставки по річним депозитам)</span>
                </div>
                <select class="js-copy-mainoid-from-s form-control form-control-sm" na >
                    <option value="0">Вибрати</option>
                    <?php
                        foreach ($mainofull as $key => $value) {
                            $fieldsf  = json_decode($value->fields);
                            $analogif = json_decode($value->analogi);
                            ?><option <?=($value->pid == $fields->mainofroms) ? 'selected' : '' ?> value="<?=$value->pid?>"><?=$value->nom_d.'-'.$fieldsf->nomber.' '.str_replace('\\', '', $fieldsf->pib)?></option><?php
                        }
                    ?>
                </select>
            </div>
        </div>        
        <div class="col-md-3">
            <input type="hidden" class="js-copy-mainoid-s" value="<?=$idMaino;?>" >
            <button type="button" class="btn btn-success btn-sm w-100 js-copy-maino-btn-s">Заповнити данні</button>
        </div>
    </div>    
    <div class="row mb-3">
        <div class="col">
            <button class="btn btn-info w-100" type="button" data-toggle="collapse" data-target="#tab1" aria-expanded="false" aria-controls="tab1">Загальна інформація</button>
        </div>
        <div class="col">
            <button class="btn btn-info w-100" type="button" data-toggle="collapse" data-target="#tab2" aria-expanded="false" aria-controls="tab2">Ідентифікація</button>
        </div>        
        <div class="col">
            <button class="btn btn-info w-100" type="button" data-toggle="collapse" data-target="#tab3" aria-expanded="false" aria-controls="tab3">Характеристики</button>
        </div>       
        <div class="col">
            <button class="btn btn-info w-100" type="button" data-toggle="collapse" data-target="#tab4" aria-expanded="false" aria-controls="tab4">Аналіз</button>
        </div>        
        <div class="col">
            <button class="btn btn-info w-100" type="button" data-toggle="collapse" data-target="#tab5" aria-expanded="false" aria-controls="tab5">Аналоги</button>
        </div>        
        <div class="col">
            <button class="btn btn-info w-100" type="button" data-toggle="collapse" data-target="#tab6" aria-expanded="false" aria-controls="tab6">Коефіцієнти</button>
        </div>        
        <div class="col">
            <button class="btn btn-info w-100" type="button" data-toggle="collapse" data-target="#tab7" aria-expanded="false" aria-controls="tab7">Розрахунок</button>
        </div>      
        <div class="col">
            <button class="btn btn-info w-100" type="button" data-toggle="collapse" data-target="#tabf" aria-expanded="false" aria-controls="tabf">Файли справи</button>
        </div>        
    </div>

<form action="<?=$current_url;?>" method="POST" id="savepropert" enctype="multipart/form-data">
    <input name="savepropert" type="hidden">
    <input type="hidden" name="idpropert" value="<?=$Propert->id;?>">
    <input type="hidden" name="idmaino"   value="<?=$idMaino;?>" >
    <input type="hidden" name="datework"  value="<?=$Maino->datework;?>" id="datework" >
    <input type="hidden" name="field[oldnomber]" value="<?=$fields->oldnomber?>" id="js-oldzvit" >
    <input type="hidden" name="field[termindii]" value="<?=$fields->termindii?>" id="js-termindii" >
    <input type="hidden" name="field[kursnbu]" value="<?=$valCurUSD->rate;?>">
    <input type="hidden" name="field[kursnbueur]" value="<?=$valCurEUR->rate;?>">
    <input type="hidden" name="field[mainofrom]" value="<?=$fields->mainofrom;?>">
    <input type="hidden" name="field[mainofroms]" value="<?=$fields->mainofroms;?>">

    <input type="hidden" class="js-field-nomber-save" name="field[nomber]" value="<?=($fields->nomber != '' ? $fields->nomber : str_replace($reestr->nomber.'-','', $Maino->nomber));?>">
    <input type="hidden" id="dateworkend" name="field[dateworkend]" value="<?=$fields->dateworkend;?>">

    <div class="row">
        <div class="col-lg-12">
            <div class="card forms"  id="accordionExample">
                <div class="collapse show" id="tab1"  data-parent="#accordionExample">
                    <div class="card-header d-flex align-items-center">
                        <h4>Загальна інформація</h4>
                    </div>
                    <div class="card-body">
                         <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Тип квартири</label>
                                    <div class="col-sm-8">
                                        <select class="form-control form-control-sm"  name="field[kvart]">
                                            <option <?php if ($fields->kvart == 'однокімнатну') { echo 'selected'; } ?> value="однокімнатну">однокімнатна</option>
                                            <option <?php if ($fields->kvart == 'двокімнатну') { echo 'selected'; } ?> value="двокімнатну">двокімнатна</option>
                                            <option <?php if ($fields->kvart == 'трьохкімнатну') { echo 'selected'; } ?> value="трьохкімнатну">трьохкімнатна</option>
                                            <option <?php if ($fields->kvart == 'чотирьохкімнатну') { echo 'selected'; } ?> value="чотирьохкімнатну">чотирьохкімнатна</option>
                                            <option <?php if ($fields->kvart == 'п\'ятикімнатну') { echo 'selected'; } ?> value="п'ятикімнатну">п'ятикімнатна</option>
                                        </select>
                                    </div>
                                </div>
                            </div>       
                            <div class="col-sm-3">
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">№ будинку</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control form-control-sm" name="field[budinok]" value="<?=$fields->budinok;?>">
                                    </div>
                                </div>
                            </div>                                     
                            <div class="col-sm-3">
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">№ квартири</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control form-control-sm" name="field[nomber_kv]" value="<?=$fields->nomber_kv;?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Площа</label>
                                    <div class="col-sm-8">
                                        <input type="number" step="0.01" class="form-control form-control-sm js-plosha_0 text-left" name="field[plosha]" value="<?=$fields->plosha;?>">
                                    </div>
                                </div>
                            </div>                        
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-2 form-control-label">Адреса</label>
                                    <div class="col-sm-10">
                                        <textarea name="field[adress]" class="form-control form-control-sm"><?=$fields->adress;?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-2 form-control-label">Проект / назва комплексу</label>
                                    <div class="col-sm-10">
                                        <textarea name="field[proekt]" class="form-control form-control-sm"><?=str_replace('\\','',$fields->proekt);?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 hide">
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Власник</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control form-control-sm" name="field[vlasnik]" value="<?=str_replace('\\','',$fields->vlasnik);?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row hide">
                            <label class="col-sm-2 form-control-label">Замовник
                            </label>
                            <div class="col-sm-10">
                                <textarea name="field[zamovnik]" class="form-control form-control-sm"><?=$fields->zamovnik;?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="tab2" data-parent="#accordionExample">
                    <div class="card-header d-flex align-items-center">
                        <h4>ІДЕНТИФІКАЦІЯ ОБ’ЄКТА ОЦІНКИ майнові права належать</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">П.I.Б.</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control form-control-sm" name="field[pib]" value="<?=str_replace(array('\\'),'',$fields->pib);?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">свідоцтві про участь у ФФБ </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control form-control-sm" name="field[ffb]" value='<?=str_replace(array('\\'),'',$fields->ffb);?>'>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">№ договору</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control form-control-sm" name="field[dogovir]" value="<?=$fields->dogovir;?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">дата договору</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control form-control-sm js-date" name="field[dogovir_date]" value="<?=$fields->dogovir_date;?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">№ свідоцтва</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control form-control-sm" name="field[nomersvid]" value="<?=$fields->nomersvid;?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">дата свідоцтва</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control form-control-sm js-date" name="field[nomersvid_date]" value="<?=$fields->nomersvid_date;?>">
                                    </div>
                                </div>
                            </div>  
                        </div>
                        <div class="form-group row hide">
                            <div class="col-sm-12">
                                <textarea name="field[prava]"  rows="5" class="form-control form-control-sm"><?=$fields->prava;?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="tab3" data-parent="#accordionExample">
                    <div class="card-header d-flex align-items-center">
                        <h4>ЗАГАЛЬНА ХАРАКТЕРИСТИКА МІСЦЕРОЗТАШУВАННЯ</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <textarea name="field[location]" rows="5" class="form-control form-control-sm"><?=$fields->location;?></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">фото мапа</label>
                            <div class="col-sm-8">
                                <input type="file" multiple="false" class="form-control form-control-sm" name="file" value="">
                            </div>
                            <div class="col-sm-2">
                            <?php 
                                if ($foto_map != '') {
                                    ?>
                                        <a href="<?=$foto_map;?>" target="_blank"><img style="height:50px;" src="<?=$foto_map;?>" alt=""></a>
                                    <?php
                                }
                            ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <h5>Відстань до</h5>
                            </div>
                            <div class="col">
                                <div class="input-group input-group-sm mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">метро</span>
                                    </div>
                                    <input type="number" step="0.1" class="form-control form-control-sm" name="field[metro]" value="<?=$fields->metro;?>">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">назва станціі</span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm" name="field[metroname]" value="<?=$fields->metroname;?>">
                                </div>
                            </div>
                            <div class="col">
                                <div class="input-group input-group-sm mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">центу міста</span>
                                    </div>
                                        <input type="number" step="0.1" class="form-control form-control-sm" name="field[city]" value="<?=$fields->city;?>">
                                </div>
                            </div>
                            <div class="col">
                                <div class="input-group input-group-sm mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">аеропорту</span>
                                    </div>
                                        <input type="number" step="0.1" class="form-control form-control-sm" name="field[aeroport]" value="<?=$fields->aeroport;?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="input-group input-group-sm mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Характеристика району</span>
                                    </div>
                                        <input type="text" class="form-control form-control-sm" name="field[transroz]" value="<?=$fields->transroz;?>">
                                </div>
                            </div>                            
                        </div>
                    </div>
                    <div class="card-header d-flex align-items-center">
                        <h4>ТЕХНІЧНА ХАРАКТЕРИСТИКА ОБ’ЄКТА ОЦІНКИ</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <textarea rows="15" id="tehbud" class="form-control form-control-sm"><?=$tehstroika;?></textarea>
                                <textarea name="tehstroika" rows="15" id="tehbud_name" class="hide"><?=$tehstroika;?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="tab4" data-parent="#accordionExample">
                    <div class="card-header d-flex align-items-center">
                        <h4>АНАЛІЗ РИНКУ НОВОБУДОВ </h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <textarea id="analizrinky" rows="15" class="form-control form-control-sm"><?=$analizrinky;?></textarea>
                                <textarea name="analizrinky" id="analizrinky_name" rows="15" class="hide"><?=$analizrinky;?></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Джерело</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control form-control-sm" name="field[analizdgerelo]" value="<?=$fields->analizdgerelo;?>">
                            </div>
                        </div>
                    </div>
                    <div class="card-header d-flex align-items-center">
                        <h4>Середні ставки по річним депозитам в дол. США для фізичних осіб</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="input-group input-group-sm mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Період</span>
                                    </div>
                                        <input type="text" class="form-control form-control-sm" name="field[depperiod]" value="<?=$fields->depperiod;?>">
                                </div>
                            </div>             
                           <div class="col">
                                <div class="input-group input-group-sm mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">ставки по річним депозитам ссилка</span>
                                    </div>
                                        <input type="text" class="form-control form-control-sm" name="field[deplink]" value="<?=$fields->deplink;?>">
                                </div>
                            </div>   
                        </div>

                        <table class="table table-borderless table-sm">
                            <tr class="table-active text-center">
                                <th class="w-50">Період</th>
                                <th class="w-50">Значення, %</th>
                            </tr>
                            <?php 
                                if (empty($dolar_procent)) {
                                    foreach ($month_array as $key => $value) {
                                        ?>
                                            <tr>
                                                <td><input type="text" class="form-control form-control-sm" name="dolar_procent[<?=$key?>][month]" value="<?=$value?>" /></td>
                                                <td><input type="number"  step="0.01" class="form-control form-control-sm" name="dolar_procent[<?=$key?>][value]" value="0"  step="0.01" /></td>
                                            </tr>
                                        <?php
                                    }
                                } else {
                                    $dolar_procent_ser = 0;
                                    $count = 0;
                                    foreach ($dolar_procent as $key => $value) {
                                        $count++;
                                        ?>
                                            <tr>
                                                <td><input type="text" class="form-control form-control-sm" name="dolar_procent[<?=$key?>][month]" value="<?=$month_array[$key]?>" /></td>
                                                <td><input type="number"  step="0.01" class="form-control form-control-sm" name="dolar_procent[<?=$key?>][value]" value="<?=$value['value']?>"  step="0.01" /></td>
                                            </tr>
                                        <?php
                                        $dolar_procent_ser = $dolar_procent_ser + (float)$value['value'];
                                    }                                
                                    $dolar_procent_ser = round($dolar_procent_ser/$count,2);
                                }
                                $fields->dolar_procent_ser = $dolar_procent_ser;
                                $fields->dolar_procent_month = round($dolar_procent_ser/12,2);
                            ?>
                            <tr class="table-active">
                                <th class=" text-center align-vert">Середнє</th>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm" name="field[dolar_procent_ser]" value="<?=$fields->dolar_procent_ser;?>" /></td>
                            </tr>
                            <tr class="table-active">
                                <th class=" text-center align-vert">з розрахунку на 1 місяць</th>
                                <td><input type="number" readonly  step="0.01" class="form-control form-control-sm js_dolar_procent_month" name="field[dolar_procent_month]" value="<?=$fields->dolar_procent_month;?>" /></td>
                            </tr>                        
                        </table>
                    </div>

                </div>
                <div class="collapse" id="tab5" data-parent="#accordionExample">
                    <div class="card-header d-flex align-items-center">
                        <h4 class="col">Об’єкти порівняння</h4>
                        <div class="col-2"><button class="btn btn-secondary w-100 js-clear-analogi" type="button">очистити</button></div>
                    </div>
                    <div class="card-body js-analog-tabl">
                        <div >
                        <table class="table table-borderless table-sm">
                            <tr class="table-active text-center">
                                <th>&nbsp;</th>
                                <th class="align-middle">Об'єкт оцінки</th>
                                <th class="align-middle">Об'єкт порівняння №1</th>
                                <th class="align-middle">Об'єкт порівняння №2</th>
                                <th class="align-middle">Об'єкт порівняння №3</th>
                            </tr>
                            <tr>
                                <th style="width: 10%">Назва комплексу</th>
                                <td style="width: 20%">
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" readonly name="analogi[0][name]" value='<?=str_replace('\\','',$fields->proekt);?>' />
                                    </div>
                                </td>
                                <td style="width: 20%">
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[1][name]" value="<?=$analogi['1']['name']?>" />
                                    </div>
                                </td>
                                <td style="width: 20%">
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[2][name]" value="<?=$analogi['2']['name']?>" />
                                    </div>
                                </td>
                                <td style="width: 20%">
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[3][name]" value="<?=$analogi['3']['name']?>" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Джерело</th>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" readonly name="analogi[0][dgerelo]" value="<?=$analogi['0']['dgerelo']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm"  name="analogi[1][dgerelo]" value="<?=$analogi['1']['dgerelo']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[2][dgerelo]" value="<?=$analogi['2']['dgerelo']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[3][dgerelo]" value="<?=$analogi['3']['dgerelo']?>" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Місцерозташування комплексу</th>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" readonly name="analogi[0][location]" value="<?=$fields->adress;?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[1][location]" value="<?=$analogi['1']['location']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[2][location]" value="<?=$analogi['2']['location']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[3][location]" value="<?=$analogi['3']['location']?>" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Період здачі будівлі</th>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm js-date js-date_end_0 js-date_end"  data-analog='0' name="analogi[0][data_end]" value="<?=$analogi['0']['data_end']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm js-date js-date_end" data-analog='1' name="analogi[1][data_end]" value="<?=$analogi['1']['data_end']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm js-date js-date_end" data-analog='2' name="analogi[2][data_end]" value="<?=$analogi['2']['data_end']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm js-date js-date_end" data-analog='3' name="analogi[3][data_end]" value="<?=$analogi['3']['data_end']?>" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Поверховість</th>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="1" class="form-control form-control-sm js-not-null" name="analogi[0][etagi]" value="<?=number_format($analogi['0']['etagi'],0,'','')?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="1" class="form-control form-control-sm js-not-null" name="analogi[1][etagi]" value="<?=number_format($analogi['1']['etagi'],0,'','')?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="1" class="form-control form-control-sm js-not-null" name="analogi[2][etagi]" value="<?=number_format($analogi['2']['etagi'],0,'','')?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="1" class="form-control form-control-sm js-not-null" name="analogi[3][etagi]" value="<?=number_format($analogi['3']['etagi'],0,'','')?>" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Технологія будівництва:</th>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[0][tehnolog]" value="<?=$analogi['0']['tehnolog']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[1][tehnolog]" value="<?=$analogi['1']['tehnolog']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[2][tehnolog]" value="<?=$analogi['2']['tehnolog']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[3][tehnolog]" value="<?=$analogi['3']['tehnolog']?>" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Стіни:</th>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[0][stini]" value="<?=$analogi['0']['stini']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[1][stini]" value="<?=$analogi['1']['stini']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[2][stini]" value="<?=$analogi['2']['stini']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[3][stini]" value="<?=$analogi['3']['stini']?>" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Висота стелі:</th>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="0.01" class="form-control form-control-sm" name="analogi[0][visota]" value="<?=$analogi['0']['visota']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="0.01"  class="form-control form-control-sm" name="analogi[1][visota]" value="<?=$analogi['1']['visota']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="0.01"  class="form-control form-control-sm" name="analogi[2][visota]" value="<?=$analogi['2']['visota']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="0.01"  class="form-control form-control-sm" name="analogi[3][visota]" value="<?=$analogi['3']['visota']?>" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Клас комплексу</th>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[0][class]" value="<?=$analogi['0']['class']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[1][class]" value="<?=$analogi['1']['class']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[2][class]" value="<?=$analogi['2']['class']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[3][class]" value="<?=$analogi['3']['class']?>" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Площа квартири</th>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="0.01" readonly class="form-control form-control-sm" data-analog="0" name="analogi[0][plosha]" value="<?=$fields->plosha?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="0.01"  class="form-control form-control-sm js-plosha js-plosha_v_1" data-analog="1" name="analogi[1][plosha]" value="<?=$analogi['1']['plosha']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="0.01"  class="form-control form-control-sm js-plosha js-plosha_v_2" data-analog="2" name="analogi[2][plosha]" value="<?=$analogi['2']['plosha']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="0.01"  class="form-control form-control-sm js-plosha js-plosha_v_3" data-analog="3" name="analogi[3][plosha]" value="<?=$analogi['3']['plosha']?>" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Паркінг </th>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[0][parking]" value="<?=$analogi['0']['parking']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[1][parking]" value="<?=$analogi['1']['parking']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[2][parking]" value="<?=$analogi['2']['parking']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[3][parking]" value="<?=$analogi['3']['parking']?>" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Якість здачі об'єкта  </th>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[0][best]" value="<?=$analogi['0']['best']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[1][best]" value="<?=$analogi['1']['best']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[2][best]" value="<?=$analogi['2']['best']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="text" class="form-control form-control-sm" name="analogi[3][best]" value="<?=$analogi['3']['best']?>" />
                                    </div>
                                </td>
                            </tr>                        
                            <tr>
                                <th>Вартість 1 кв.м./грн.</th>
                                <td>
                                    <div class="form-group mb-0">
                                        &nbsp;
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="0.01"  class="form-control form-control-sm js-vartist js-vartist_1" name="analogi[1][price]" value="<?=$analogi['1']['price']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="0.01"  class="form-control form-control-sm js-vartist js-vartist_2" name="analogi[2][price]" value="<?=$analogi['2']['price']?>" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group mb-0">
                                        <input type="number" step="0.01"  class="form-control form-control-sm js-vartist js-vartist_3" name="analogi[3][price]" value="<?=$analogi['3']['price']?>" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-left align-middle">Середнє значення по вартості 1 кв.м. (заокруглено)</th>
                                <td>
                                    <input type="number" step="0.01"  class="form-control form-control-sm  js-ser-vartist" name="field[srednio]" value="<?=$fields->srednio;?>" />
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="tab6" data-parent="#accordionExample">
                    <div class="card-header d-flex align-items-center">
                        <h4>Період здачі в експлуатацію (К4)</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr class="table-active">
                                <th class="align-middle" rowspan="2">Значення</th>
                                <th class="align-middle">Об'єкт порівняння №1</th>
                                <th class="align-middle">Об'єкт порівняння №2</th>
                                <th class="align-middle">Об'єкт порівняння №3</th>
                            </tr>
                            <tr class="table-active text-center">
                                <td class="js-date_end_1">
                                    <?=$analogi['1']['data_end']?>
                                </td>
                                <td class="js-date_end_2">
                                    <?=$analogi['2']['data_end']?>
                                </td>
                                <td class="js-date_end_3">
                                    <?=$analogi['3']['data_end']?>
                                </td>
                            </tr>
                            <tr>
                                <th class="align-middle">Різниця між періодами ОО та ОП, міс.</th>
                                <td>
                                    <input type="text" class="form-control form-control-sm js-between_date_1 text-right" readonly name="analogi[1][between_date]" value="<?=$analogi['1']['between_date']?>" />
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm js-between_date_2 text-right" readonly name="analogi[2][between_date]" value="<?=$analogi['2']['between_date']?>" />
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm js-between_date_3 text-right" readonly name="analogi[3][between_date]" value="<?=$analogi['3']['between_date']?>" />
                                </td>
                            </tr>
                            <tr>
                                <th class="align-middle">Коефіцієнт ризику,%</th>
                                <td>
                                    <input type="number" readonly  step="0.01" class="form-control form-control-sm  js-coef_riz_1" readonly name="analogi[1][coef_riz]" value="<?=round($analogi['1']['coef_riz'],2)?>" />
                                </td>
                                <td>
                                    <input type="number" readonly  step="0.01" class="form-control form-control-sm  js-coef_riz_2" readonly name="analogi[2][coef_riz]" value="<?=round($analogi['2']['coef_riz'],2)?>" />
                                </td>
                                <td>
                                    <input type="number" readonly  step="0.01" class="form-control form-control-sm  js-coef_riz_3" readonly name="analogi[3][coef_riz]" value="<?=round($analogi['3']['coef_riz'],2)?>" />
                                </td>
                            </tr>
                            <tr>
                                <th class="align-middle">Коефіцієнт коригування</th>
                                <td>
                                    <input type="number" readonly  step="0.01" class="form-control form-control-sm  js-coef_coreg_1" readonly name="analogi[1][coef_coreg]" value="<?=round($analogi['1']['coef_coreg'],2)?>" />
                                </td>
                                <td>
                                    <input type="number" readonly  step="0.01" class="form-control form-control-sm  js-coef_coreg_2" readonly name="analogi[2][coef_coreg]" value="<?=round($analogi['2']['coef_coreg'],2)?>" />
                                </td>
                                <td>
                                    <input type="number" readonly  step="0.01" class="form-control form-control-sm  js-coef_coreg_3" readonly name="analogi[3][coef_coreg]" value="<?=round($analogi['3']['coef_coreg'],2)?>" />
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-header d-flex align-items-center">
                        <h4>Розрахунок коефіцієнту коригування в залежності від місце розташування комплексу</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr class="table-active">
                                <th class="align-middle" rowspan="2">№ з/п</th>
                                <th class="align-middle" rowspan="2">Місце розташування</th>
                                <th class="align-middle" colspan="3">Місце  розташування в межах міста</th>
                                <th class="align-middle" colspan="3">Місце розташування в межах кварталу</th>
                                <th class="align-middle" colspan="3">Наближеність до магістралей міського значення і зупинок транспорту</th>
                                <th class="align-middle" rowspan="2">Агрегована оцінка</th>
                                <th class="align-middle" rowspan="2">К7</th>
                            </tr>
                            <tr style="text-align: center; vertical-align: middle">
                                <th>Бал</th>
                                <th>Вага</th>
                                <th>Оцінка</th>
                                <th>Бал</th>
                                <th>Вага</th>
                                <th>Оцінка</th>
                                <th>Бал</th>
                                <th>Вага</th>
                                <th>Оцінка</th>
                            </tr>
                            <tr>
                                <td>-</td>
                                <td><input type="text" class="form-control form-control-sm" readonly value="<?=$analogi['0']['location']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_bal_1" data-bal_vag="1" name="analogi[0][tb7_11]" value="<?=$analogi['0']['tb7_11']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_vag_1" data-bal_vag="1" name="analogi[0][tb7_12]" value="<?=$analogi['0']['tb7_12']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_bal_vag js_oce_1" data-bal_vag="1" name="analogi[0][tb7_13]" value="<?=$analogi['0']['tb7_13']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_bal_2" data-bal_vag="2" name="analogi[0][tb7_21]" value="<?=$analogi['0']['tb7_21']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_vag_2" data-bal_vag="2" name="analogi[0][tb7_22]" value="<?=$analogi['0']['tb7_22']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_bal_vag js_oce_2" data-bal_vag="2" name="analogi[0][tb7_23]" value="<?=$analogi['0']['tb7_23']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_bal_3" data-bal_vag="3" name="analogi[0][tb7_31]" value="<?=$analogi['0']['tb7_31']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_vag_3" data-bal_vag="3" name="analogi[0][tb7_32]" value="<?=$analogi['0']['tb7_32']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_bal_vag js_oce_3" data-bal_vag="3" name="analogi[0][tb7_33]" value="<?=$analogi['0']['tb7_33']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_ocenka js_ocenka_0" name="analogi[0][ocenka]" value="<?=$analogi['0']['ocenka']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_cof_k7" name="analogi[0][k7]"    value="<?=round($analogi['0']['k7'],2)?>" /></td>
                            </tr>
                            <tr>
                                <th colspan="13" style="text-align: center; vertical-align: middle">Об’єкти порівняння</th>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td><input type="text" class="form-control form-control-sm" readonly value="<?=$analogi['1']['location']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_bal_1" data-bal_vag="1" name="analogi[1][tb7_11]"   value="<?=$analogi['1']['tb7_11']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_vag_1" data-bal_vag="1" name="analogi[1][tb7_12]"   value="<?=$analogi['1']['tb7_12']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_bal_vag js_oce_1" data-bal_vag="1" name="analogi[1][tb7_13]"   value="<?=$analogi['1']['tb7_13']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_bal_2" data-bal_vag="2" name="analogi[1][tb7_21]"   value="<?=$analogi['1']['tb7_21']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_vag_2" data-bal_vag="2" name="analogi[1][tb7_22]"   value="<?=$analogi['1']['tb7_22']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_bal_vag js_oce_2" data-bal_vag="2" name="analogi[1][tb7_23]"   value="<?=$analogi['1']['tb7_23']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_bal_3" data-bal_vag="3" name="analogi[1][tb7_31]"   value="<?=$analogi['1']['tb7_31']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_vag_3" data-bal_vag="3" name="analogi[1][tb7_32]"   value="<?=$analogi['1']['tb7_32']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_bal_vag js_oce_3" data-bal_vag="3" name="analogi[1][tb7_33]"   value="<?=$analogi['1']['tb7_33']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_ocenka js_ocenka_1" name="analogi[1][ocenka]"  value="<?=$analogi['1']['ocenka']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_cof_k7 js_cof_k7_1" name="analogi[1][k7]"      value="<?=round($analogi['1']['k7'],2)?>" /></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><input type="text" class="form-control form-control-sm" readonly value="<?=$analogi['2']['location']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_bal_1" data-bal_vag="1" name="analogi[2][tb7_11]"   value="<?=$analogi['2']['tb7_11']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_vag_1" data-bal_vag="1" name="analogi[2][tb7_12]"   value="<?=$analogi['2']['tb7_12']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_bal_vag js_oce_1" data-bal_vag="1" name="analogi[2][tb7_13]"   value="<?=$analogi['2']['tb7_13']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_bal_2" data-bal_vag="2" name="analogi[2][tb7_21]"   value="<?=$analogi['2']['tb7_21']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_vag_2" data-bal_vag="2" name="analogi[2][tb7_22]"   value="<?=$analogi['2']['tb7_22']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_bal_vag js_oce_2" data-bal_vag="2" name="analogi[2][tb7_23]"   value="<?=$analogi['2']['tb7_23']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_bal_3" data-bal_vag="3" name="analogi[2][tb7_31]"   value="<?=$analogi['2']['tb7_31']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_vag_3" data-bal_vag="3" name="analogi[2][tb7_32]"   value="<?=$analogi['2']['tb7_32']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_bal_vag js_oce_3" data-bal_vag="3" name="analogi[2][tb7_33]"   value="<?=$analogi['2']['tb7_33']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_ocenka js_ocenka_2" name="analogi[2][ocenka]"   value="<?=$analogi['2']['ocenka']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_cof_k7 js_cof_k7_2" name="analogi[2][k7]"       value="<?=round($analogi['2']['k7'],2)?>" /></td>
                            </tr>
                            <tr>
                            <td>2</td>
                                <td><input type="text" class="form-control form-control-sm" readonly value="<?=$analogi['3']['location']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_bal_1" data-bal_vag="1" name="analogi[3][tb7_11]"   value="<?=$analogi['3']['tb7_11']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_vag_1" data-bal_vag="1" name="analogi[3][tb7_12]"   value="<?=$analogi['3']['tb7_12']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_bal_vag js_oce_1" data-bal_vag="1" name="analogi[3][tb7_13]"   value="<?=$analogi['3']['tb7_13']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_bal_2" data-bal_vag="2" name="analogi[3][tb7_21]"   value="<?=$analogi['3']['tb7_21']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_vag_2" data-bal_vag="2" name="analogi[3][tb7_22]"   value="<?=$analogi['3']['tb7_22']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_bal_vag js_oce_2" data-bal_vag="2" name="analogi[3][tb7_23]"   value="<?=$analogi['3']['tb7_23']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_bal_3" data-bal_vag="3" name="analogi[3][tb7_31]"   value="<?=$analogi['3']['tb7_31']?>" /></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm js_bal_vag js_vag_3" data-bal_vag="3" name="analogi[3][tb7_32]"   value="<?=$analogi['3']['tb7_32']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_bal_vag js_oce_3" data-bal_vag="3" name="analogi[3][tb7_33]"   value="<?=$analogi['3']['tb7_33']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_ocenka js_ocenka_3" name="analogi[3][ocenka]"   value="<?=$analogi['3']['ocenka']?>" /></td>
                                <td><input type="number" readonly class="form-control form-control-sm js_cof_k7 js_cof_k7_3" name="analogi[3][k7]"       value="<?=round($analogi['3']['k7'],2)?>" /></td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-header d-flex align-items-center">
                        <h4>Розрахунок коефіцієнту коригування в залежності від площі квартири</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr style="text-align: center; vertical-align: middle">
                                <th class="w-25 align-middle">Найменування</th>
                                <th class="w-15 align-middle">Площа, кв. м</th>
                                <th class="w-15 align-middle">Вартість пропозиції за 1 кв. м, грн.</th>
                                <th class="w-15 align-middle">Відношення площ</th>
                                <th class="w-15 align-middle">Коригування на основі кореляційно-регресивного аналізу</th>
                                <th class="w-15 align-middle">К8</th>
                            </tr>
                            <tr>
                                <td>Об'єкт оцінки</td>
                                <td style="text-align: center; vertical-align: middle"><?=number_format($fields->plosha, 2, ',', '');?></td>
                                <td style="text-align: center; vertical-align: middle">-</td>
                                <td style="text-align: center; vertical-align: middle">-</td>
                                <td style="text-align: center; vertical-align: middle">-</td>
                                <td style="text-align: center; vertical-align: middle">-</td>
                            </tr>
                            <tr>
                                <td>Об'єкт порівняння №1</td>
                                <td style="text-align: center; vertical-align: middle"><?=number_format($analogi['1']['plosha'], 2, ',', '')?></td>
                                <td style="text-align: center; vertical-align: middle"><?=number_format($analogi['1']['price'], 2, ',', '')?></td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-plosha_1" name="analogi[1][vdinplosh]"  value="<?=round($analogi['1']['vdinplosh'],2)?>" /></td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-koreguvana_1" name="analogi[1][koreguvana]" value="<?=round($analogi['1']['koreguvana'],2)?>" /></td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-koreguvana_1" name="analogi[1][k8]"         value="<?=round($analogi['1']['k8'],2)?>" /></td>
                            </tr>
                            <tr>
                                <td>Об'єкт порівняння №2</td>
                                <td style="text-align: center; vertical-align: middle"><?=number_format($analogi['2']['plosha'], 2, ',', '')?></td>
                                <td style="text-align: center; vertical-align: middle"><?=number_format($analogi['2']['price'], 2, ',', '')?></td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-plosha_2" name="analogi[2][vdinplosh]"  value="<?=$analogi['2']['vdinplosh']?>" /></td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-koreguvana_2" name="analogi[2][koreguvana]" value="<?=$analogi['2']['koreguvana']?>" /></td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-koreguvana_2" name="analogi[2][k8]"         value="<?=$analogi['2']['k8']?>" /></td>
                            </tr>
                            <tr>
                                <td>Об'єкт порівняння №3</td>
                                <td style="text-align: center; vertical-align: middle"><?=number_format($analogi['3']['plosha'], 2, ',', '')?></td>
                                <td style="text-align: center; vertical-align: middle"><?=number_format($analogi['3']['price'], 2, ',', '')?></td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-plosha_3" name="analogi[3][vdinplosh]"      value="<?=round($analogi['3']['vdinplosh'],2)?>" /></td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-koreguvana_3" name="analogi[3][koreguvana]" value="<?=round($analogi['3']['koreguvana'],2)?>" /></td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-koreguvana_3" name="analogi[3][k8]"         value="<?=round($analogi['3']['k8'],2)?>" /></td>
                            </tr>
                            <tr>
                                <th colspan="2">Коефіцієнт гальмування</th>
                                <th style="text-align: center; vertical-align: middle">-0,1</th>
                                <th style="text-align: center; vertical-align: middle" colspan="3">Характер зв'язку</th>
                            </tr>
                            <tr>
                                <th colspan="2">Коефіцієнт кореляції</th>
                                <th style="text-align: center; vertical-align: middle"><input type="number" readonly class="form-control form-control-sm js-koreljc" name="raschet[koreljc]" value="<?=round($raschet['koreljc'],2)?>" /></th>
                                <td style="text-align: center; vertical-align: middle" colspan="3">Дуже сильний</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="collapse" id="tab7" data-parent="#accordionExample">
                    <div class="card-header d-flex align-items-center">
                        <h4>Розрахунок ринкової вартості об'єкта оцінки порівняльним підходом</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <thead>
                                <tr class="table-active" style="text-align: center; vertical-align: middle">
                                    <th class="w-20 align-middle">Характеристика поправок</th>
                                    <th class="w-20 align-middle">Об'єкт оцінки</th>
                                    <th class="w-20 align-middle">Об'єкт порівняння №1</th>
                                    <th class="w-20 align-middle">Об'єкт порівняння №2</th>
                                    <th class="w-20 align-middle">Об'єкт порівняння №3</th>
                                </tr>
                            </thead>
                            <tr>
                                <th>Площа, кв. м </th>
                                <td><?=number_format($fields->plosha, 2, ',', '');?></td>
                                <td><?=number_format($analogi['1']['plosha'], 2, ',', '')?></td>
                                <td><?=number_format($analogi['2']['plosha'], 2, ',', '')?></td>
                                <td><?=number_format($analogi['3']['plosha'], 2, ',', '')?></td>
                            </tr>
                            <tr>
                                <th>Тип прав</th>
                                <td>майнові права</td>
                                <td>майнові права</td>
                                <td>майнові права</td>
                                <td>майнові права</td>
                            </tr>
                            <tr class="js-tr_calc_price_0">
                                <th>Вартість, грн. / 1 кв. м</th>
                                <td>&nbsp;</td>
                                <td><?=number_format($analogi['1']['price'], 2, ',', '')?> <input type="hidden" data-analog="1" value="<?=$analogi['1']['price']?>" /></td>
                                <td><?=number_format($analogi['2']['price'], 2, ',', '')?> <input type="hidden" data-analog="2" value="<?=$analogi['2']['price']?>" /></td>
                                <td><?=number_format($analogi['3']['price'], 2, ',', '')?> <input type="hidden" data-analog="3" value="<?=$analogi['3']['price']?>" /></td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">КОРИГУВАННЯ</th>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">Умови фінансування (К1)</th>
                            </tr>
                            <tr>
                                <th>Умови продажу</th>
                                <td>Типові умови відкритого ринку</td>
                                <td>Типові умови відкритого ринку</td>
                                <td>Типові умови відкритого ринку</td>
                                <td>Типові умови відкритого ринку</td>
                            </tr>
                            <tr>
                                <th>Типові умови ринку</th>
                                <td>Одноразовий платіж/відстрочка платежу</td>
                                <td>Одноразовий платіж/відстрочка платежу</td>
                                <td>Одноразовий платіж/відстрочка платежу</td>
                                <td>Одноразовий платіж/відстрочка платежу</td>
                            </tr>
                            <tr>
                                <th>Коефіцієнт коригування</th>
                                <td rowspan="2">&nbsp;</td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="1" data-nomer="0" step="0.01" name="analogi[1][koff1]" value="<?=((float)$analogi['1']['koff1'] <= 0 ? '1' : $analogi['1']['koff1'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="2" data-nomer="0" step="0.01" name="analogi[2][koff1]" value="<?=((float)$analogi['2']['koff1'] <= 0 ? '1' : $analogi['2']['koff1'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="3" data-nomer="0" step="0.01" name="analogi[3][koff1]" value="<?=((float)$analogi['3']['koff1'] <= 0 ? '1' : $analogi['3']['koff1'])?>"> </td>
                            </tr>
                            <tr class="js-tr_calc_price_1">
                                <th>Скоригована ціна за 1 кв. м</th>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="1" name="analogi[1][kk1]" value="<?=round($analogi['1']['kk1'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="2" name="analogi[2][kk1]" value="<?=round($analogi['2']['kk1'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="3" name="analogi[3][kk1]" value="<?=round($analogi['3']['kk1'],2)?>"> </td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">Юридичний статус (К2)</th>
                            </tr>
                            <tr class="text-center">
                                <th class="text-left">Значення</th>
                                <td>майнові права</td>
                                <td>майнові права</td>
                                <td>майнові права</td>
                                <td>майнові права</td>
                            </tr>
                            <tr>
                                <th>Коефіцієнт коригування</th>
                                <td rowspan="2">&nbsp;</td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="1" data-nomer="1" step="0.01"  name="analogi[1][koff2]"  value="<?=((float)$analogi['1']['koff2'] <= 0 ? '1' : $analogi['1']['koff2'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="2" data-nomer="1" step="0.01"  name="analogi[2][koff2]"  value="<?=((float)$analogi['2']['koff2'] <= 0 ? '1' : $analogi['2']['koff2'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="3" data-nomer="1" step="0.01"  name="analogi[3][koff2]"  value="<?=((float)$analogi['3']['koff2'] <= 0 ? '1' : $analogi['3']['koff2'])?>"> </td>
                            </tr>
                            <tr class="js-tr_calc_price_2">
                                <th>Скоригована ціна за 1 кв. м</th>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="1"  name="analogi[1][kk2]"  value="<?=round($analogi['1']['kk2'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="2"  name="analogi[2][kk2]"  value="<?=round($analogi['2']['kk2'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="3"  name="analogi[3][kk2]"  value="<?=round($analogi['3']['kk2'],2)?>"> </td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">Умови продажу (К3)</th>
                            </tr>
                            <tr class="text-center">
                                <th class="text-left">Значення</th>
                                <td>&nbsp;</td>
                                <td>Пропозиція від забудовника</td>
                                <td>Пропозиція від забудовника</td>
                                <td>Пропозиція від забудовника</td>
                            </tr>
                            <tr>
                                <th>Коефіцієнт коригування</th>
                                <td rowspan="2">&nbsp;</td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="1" data-nomer="2" step="0.01"  name="analogi[1][koff3]"  value="<?=((float)$analogi['1']['koff3'] <= 0 ? '1' : $analogi['1']['koff3'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="2" data-nomer="2" step="0.01"  name="analogi[2][koff3]"  value="<?=((float)$analogi['2']['koff3'] <= 0 ? '1' : $analogi['2']['koff3'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="3" data-nomer="2" step="0.01"  name="analogi[3][koff3]"  value="<?=((float)$analogi['3']['koff3'] <= 0 ? '1' : $analogi['3']['koff3'])?>"> </td>
                            </tr>
                            <tr class="js-tr_calc_price_3">
                                <th>Скоригована ціна за 1 кв. м</th>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="1"  name="analogi[1][kk3]"  value="<?=round($analogi['1']['kk3'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="2"  name="analogi[2][kk3]"  value="<?=round($analogi['2']['kk3'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="3"  name="analogi[3][kk3]"  value="<?=round($analogi['3']['kk3'],2)?>"> </td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">Період здачі в експлуатацію (К4)</th>
                            </tr>
                            <tr class="text-center">
                                <th class="text-left">Значення</th>
                                <td><?=$analogi['0']['data_end']?></td>
                                <td><?=$analogi['1']['data_end']?></td>
                                <td><?=$analogi['2']['data_end']?></td>
                                <td><?=$analogi['3']['data_end']?></td>
                            </tr>
                            <tr>
                                <th>Коефіцієнт коригування</th>
                                <td rowspan="2">&nbsp;</td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-calc_big js-coef_coreg_1" data-analog="1" data-nomer="3" step="0.01"  name="analogi[1][koff4]"  value="<?=$analogi['1']['koff4']?>"> </td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-calc_big js-coef_coreg_2" data-analog="2" data-nomer="3" step="0.01"  name="analogi[2][koff4]"  value="<?=$analogi['2']['koff4']?>"> </td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-calc_big js-coef_coreg_3" data-analog="3" data-nomer="3" step="0.01"  name="analogi[3][koff4]"  value="<?=$analogi['3']['koff4']?>"> </td>
                            </tr>
                            <tr class="js-tr_calc_price_4">
                                <th>Скоригована ціна за 1 кв. м</th>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="1"  name="analogi[1][kk4]"  value="<?=round($analogi['1']['kk4'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="2"  name="analogi[2][kk4]"  value="<?=round($analogi['2']['kk4'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="3"  name="analogi[3][kk4]"  value="<?=round($analogi['3']['kk4'],2)?>"> </td>

                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">Технологія будівництва (К5)</th>
                            </tr>
                            <tr class="text-center">
                                <th class="text-left">Значення</th>
                                <td><?=$analogi['0']['tehnolog']?></td>
                                <td><?=$analogi['1']['tehnolog']?></td>
                                <td><?=$analogi['2']['tehnolog']?></td>
                                <td><?=$analogi['3']['tehnolog']?></td>
                            </tr>
                            <tr>
                                <th>Коефіцієнт коригування</th>
                                <td rowspan="2">&nbsp;</td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="1" data-nomer="4" step="0.01"  name="analogi[1][koff5]"  value="<?=((float)$analogi['1']['koff5'] <= 0 ? '1' : $analogi['1']['koff5'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="2" data-nomer="4" step="0.01"  name="analogi[2][koff5]"  value="<?=((float)$analogi['2']['koff5'] <= 0 ? '1' : $analogi['2']['koff5'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="3" data-nomer="4" step="0.01"  name="analogi[3][koff5]"  value="<?=((float)$analogi['3']['koff5'] <= 0 ? '1' : $analogi['3']['koff5'])?>"> </td>
                            </tr>
                            <tr class="js-tr_calc_price_5">
                                <th>Скоригована ціна за 1 кв. м</th>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="1"  name="analogi[1][kk5]"  value="<?=round($analogi['1']['kk5'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="2"  name="analogi[2][kk5]"  value="<?=round($analogi['2']['kk5'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="3"  name="analogi[3][kk5]"  value="<?=round($analogi['3']['kk5'],2)?>"> </td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">Матеріал стін (К6)</th>
                            </tr>
                            <tr class="text-center">
                                <th class="text-left">Значення</th>
                                <td><?=$analogi['0']['stini']?></td>
                                <td><?=$analogi['1']['stini']?></td>
                                <td><?=$analogi['2']['stini']?></td>
                                <td><?=$analogi['3']['stini']?></td>
                            </tr>
                            <tr>
                                <th>Коефіцієнт коригування</th>
                                <td rowspan="2">&nbsp;</td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="1" data-nomer="5" step="0.01"  name="analogi[1][koff6]"  value="<?=((float)$analogi['1']['koff6'] <= 0 ? '1' : $analogi['1']['koff6'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="2" data-nomer="5" step="0.01"  name="analogi[2][koff6]"  value="<?=((float)$analogi['2']['koff6'] <= 0 ? '1' : $analogi['2']['koff6'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="3" data-nomer="5" step="0.01"  name="analogi[3][koff6]"  value="<?=((float)$analogi['3']['koff6'] <= 0 ? '1' : $analogi['3']['koff6'])?>"> </td>
                            </tr>
                            <tr class="js-tr_calc_price_6">
                                <th>Скоригована ціна за 1 кв. м</th>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="1"  name="analogi[1][kk6]"  value="<?=round($analogi['1']['kk6'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="2"  name="analogi[2][kk6]"  value="<?=round($analogi['2']['kk6'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="3"  name="analogi[3][kk6]"  value="<?=round($analogi['3']['kk6'],2)?>"> </td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">Місце розташування комплексу (К7)</th>
                            </tr>
                            <tr class="text-center">
                                <th class="text-left">Значення</th>
                                <td><?=$analogi['0']['location']?></td>
                                <td><?=$analogi['1']['location']?></td>
                                <td><?=$analogi['2']['location']?></td>
                                <td><?=$analogi['3']['location']?></td>
                            </tr>
                            <tr>
                                <th>Коефіцієнт коригування</th>
                                <td rowspan="2">&nbsp;</td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-calc_big js_cof_k7_1" data-analog="1" data-nomer="6" step="0.01"  name="analogi[1][koff7]"  value="<?=$analogi['1']['koff7']?>"> </td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-calc_big js_cof_k7_2" data-analog="2" data-nomer="6" step="0.01"  name="analogi[2][koff7]"  value="<?=$analogi['2']['koff7']?>"> </td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-calc_big js_cof_k7_3" data-analog="3" data-nomer="6" step="0.01"  name="analogi[3][koff7]"  value="<?=$analogi['3']['koff7']?>"> </td>
                            </tr>
                            <tr class="js-tr_calc_price_7">
                                <th>Скоригована ціна за 1 кв. м</th>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="1"  name="analogi[1][kk7]"  value="<?=round($analogi['1']['kk7'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="2"  name="analogi[2][kk7]"  value="<?=round($analogi['2']['kk7'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="3"  name="analogi[3][kk7]"  value="<?=round($analogi['3']['kk7'],2)?>"> </td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">Фізичні розміри (К8)</th>
                            </tr>
                            <tr class="text-center">
                                <th class="text-left">Значення</th>
                                <td><?=number_format($analogi['0']['plosha'], 2, ',', '')?></td>
                                <td><?=number_format($analogi['1']['plosha'], 2, ',', '')?></td>
                                <td><?=number_format($analogi['2']['plosha'], 2, ',', '')?></td>
                                <td><?=number_format($analogi['3']['plosha'], 2, ',', '')?></td>
                            </tr>
                            <tr>
                                <th>Коефіцієнт коригування</th>
                                <td rowspan="2">&nbsp;</td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-calc_big js-koreguvana_1" data-analog="1" data-nomer="7" step="0.01"  name="analogi[1][koff8]"  value="<?=$analogi['1']['koff8']?>"> </td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-calc_big js-koreguvana_2" data-analog="2" data-nomer="7" step="0.01"  name="analogi[2][koff8]"  value="<?=$analogi['2']['koff8']?>"> </td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-calc_big js-koreguvana_3" data-analog="3" data-nomer="7" step="0.01"  name="analogi[3][koff8]"  value="<?=$analogi['3']['koff8']?>"> </td>
                            </tr>
                            <tr class="js-tr_calc_price_8">
                                <th>Скоригована ціна за 1 кв. м</th>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="1"  name="analogi[1][kk8]"  value="<?=round($analogi['1']['kk8'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="2"  name="analogi[2][kk8]"  value="<?=round($analogi['2']['kk8'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="3"  name="analogi[3][kk8]"  value="<?=round($analogi['3']['kk8'],2)?>"> </td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">Висота стелі (К9)</th>
                            </tr>
                            <tr class="text-center">
                                <th class="text-left">Значення</th>
                                <td><?=number_format($analogi['0']['visota'], 2, ',', '')?></td>
                                <td><?=number_format($analogi['1']['visota'], 2, ',', '')?></td>
                                <td><?=number_format($analogi['2']['visota'], 2, ',', '')?></td>
                                <td><?=number_format($analogi['3']['visota'], 2, ',', '')?></td>
                            </tr>
                            <tr>
                                <th>Коефіцієнт коригування</th>
                                <td rowspan="2">&nbsp;</td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="1" data-nomer="8" step="0.01"  name="analogi[1][koff9]"  value="<?=((float)$analogi['1']['koff9'] <= 0 ? '1' : $analogi['1']['koff9'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="2" data-nomer="8" step="0.01"  name="analogi[2][koff9]"  value="<?=((float)$analogi['2']['koff9'] <= 0 ? '1' : $analogi['2']['koff9'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="3" data-nomer="8" step="0.01"  name="analogi[3][koff9]"  value="<?=((float)$analogi['3']['koff9'] <= 0 ? '1' : $analogi['3']['koff9'])?>"> </td>
                            </tr>
                            <tr class="js-tr_calc_price_9">
                                <th>Скоригована ціна за 1 кв. м</th>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="1"  name="analogi[1][kk9]"  value="<?=round($analogi['1']['kk9'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="2"  name="analogi[2][kk9]"  value="<?=round($analogi['2']['kk9'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="3"  name="analogi[3][kk9]"  value="<?=round($analogi['3']['kk9'],2)?>"> </td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">Паркінг (К10)</th>
                            </tr>
                            <tr class="text-center">
                                <th class="text-left">Значення</th>
                                <td><?=$analogi['0']['parking']?></td>
                                <td><?=$analogi['1']['parking']?></td>
                                <td><?=$analogi['2']['parking']?></td>
                                <td><?=$analogi['3']['parking']?></td>
                            </tr>
                            <tr>
                                <th>Коефіцієнт коригування</th>
                                <td rowspan="2">&nbsp;</td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="1" data-nomer="9" step="0.01"  name="analogi[1][koff10]"  value="<?=((float)$analogi['1']['koff10'] <= 0 ? '1' : $analogi['1']['koff10'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="2" data-nomer="9" step="0.01"  name="analogi[2][koff10]"  value="<?=((float)$analogi['2']['koff10'] <= 0 ? '1' : $analogi['2']['koff10'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="3" data-nomer="9" step="0.01"  name="analogi[3][koff10]"  value="<?=((float)$analogi['3']['koff10'] <= 0 ? '1' : $analogi['3']['koff10'])?>"> </td>
                            </tr>
                            <tr class="js-tr_calc_price_10">
                                <th>Скоригована ціна за 1 кв. м</th>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="1"  name="analogi[1][kk10]"  value="<?=round($analogi['1']['kk10'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="2"  name="analogi[2][kk10]"  value="<?=round($analogi['2']['kk10'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="3"  name="analogi[3][kk10]"  value="<?=round($analogi['3']['kk10'],2)?>"> </td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">Якість здачі об'єкта (К11)</th>
                            </tr>
                            <tr class="text-center">
                                <th class="text-left">Значення</th>
                                <td><?=$analogi['0']['best']?></td>
                                <td><?=$analogi['1']['best']?></td>
                                <td><?=$analogi['2']['best']?></td>
                                <td><?=$analogi['3']['best']?></td>
                            </tr>
                            <tr>
                                <th>Коефіцієнт коригування</th>
                                <td rowspan="2">&nbsp;</td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="1" data-nomer="10" step="0.01"  name="analogi[1][koff11]"  value="<?=((float)$analogi['1']['koff11'] <= 0 ? '1' : $analogi['1']['koff11'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="2" data-nomer="10" step="0.01"  name="analogi[2][koff11]"  value="<?=((float)$analogi['2']['koff11'] <= 0 ? '1' : $analogi['2']['koff11'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="3" data-nomer="10" step="0.01"  name="analogi[3][koff11]"  value="<?=((float)$analogi['3']['koff11'] <= 0 ? '1' : $analogi['3']['koff11'])?>"> </td>
                            </tr>
                            <tr class="js-tr_calc_price_11">
                                <th>Скоригована ціна за 1 кв. м</th>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="1"  name="analogi[1][kk11]"  value="<?=round($analogi['1']['kk11'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="2"  name="analogi[2][kk11]"  value="<?=round($analogi['2']['kk11'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm text-right" data-analog="3"  name="analogi[3][kk11]"  value="<?=round($analogi['3']['kk11'],2)?>"> </td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">Характеристика інженерних комунікацій (К12)</th>
                            </tr>
                            <tr class="text-center">
                                <th class="text-left">Електропостачання</th>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[0][elec]" <?=($analogi['0']['elec'] == 0 ? "checked" : "") ?> value="0" id="exampleCheck111">
                                    <label class="form-check-label" for="exampleCheck111">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[0][elec]" <?=($analogi['0']['elec'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck112">
                                    <label class="form-check-label" for="exampleCheck112">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[1][elec]" <?=($analogi['1']['elec'] == 0 ? "checked" : "") ?> value="0" id="exampleCheck121">
                                    <label class="form-check-label" for="exampleCheck121">є</label>
                                    &nbsp; / &nbsp; 
                                    <input type="radio" class="form-check-input" name="analogi[1][elec]" <?=($analogi['1']['elec'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck122">
                                    <label class="form-check-label" for="exampleCheck122">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[2][elec]" <?=($analogi['2']['elec'] == 0 ? "checked" : "") ?> value="0" id="exampleCheck131">
                                    <label class="form-check-label" for="exampleCheck131">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[2][elec]" <?=($analogi['2']['elec'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck132">
                                    <label class="form-check-label" for="exampleCheck132">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[3][elec]" <?=($analogi['3']['elec'] == 0 ? "checked" : "") ?> value="0" id="exampleCheck141">
                                    <label class="form-check-label" for="exampleCheck141">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[3][elec]" <?=($analogi['3']['elec'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck142">
                                    <label class="form-check-label" for="exampleCheck142">немає</label> 
                                </td>
                            </tr>
                            <tr class="text-center">
                                <th class="text-left">Газопостачання</th>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[0][gaz]" <?=($analogi['0']['gaz'] == 0 ? "checked" : "") ?> value="0" id="exampleCheck211">
                                    <label class="form-check-label" for="exampleCheck211">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[0][gaz]" <?=($analogi['0']['gaz'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck212">
                                    <label class="form-check-label" for="exampleCheck212">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[1][gaz]" <?=($analogi['1']['gaz'] == 0 ? "checked" : "") ?> value="0" id="exampleCheck221">
                                    <label class="form-check-label" for="exampleCheck221">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[1][gaz]" <?=($analogi['1']['gaz'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck222">
                                    <label class="form-check-label" for="exampleCheck222">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[2][gaz]" <?=($analogi['2']['gaz'] == 0 ? "checked" : "") ?> value="0" id="exampleCheck331">
                                    <label class="form-check-label" for="exampleCheck331">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[2][gaz]" <?=($analogi['2']['gaz'] == 01 ? "checked" : "") ?> value="1" id="exampleCheck332">
                                    <label class="form-check-label" for="exampleCheck332">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[3][gaz]" <?=($analogi['3']['gaz'] == 0 ? "checked" : "") ?> value="0" id="exampleCheck441">
                                    <label class="form-check-label" for="exampleCheck441">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[3][gaz]" <?=($analogi['3']['gaz'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck442">
                                    <label class="form-check-label" for="exampleCheck442">немає</label> 
                                </td>
                            </tr>                        
                            <tr class="text-center">
                                <th class="text-left">Водопостачання</th>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[0][voda]" <?=($analogi['0']['voda'] == 0 ? "checked" : "") ?> value="0" id="exampleCheck511">
                                    <label class="form-check-label" for="exampleCheck511">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[0][voda]" <?=($analogi['0']['voda'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck512">
                                    <label class="form-check-label" for="exampleCheck512">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[1][voda]" <?=($analogi['1']['voda'] == 0 ? "checked" : "") ?> value="0" id="exampleCheck521">
                                    <label class="form-check-label" for="exampleCheck521">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[1][voda]" <?=($analogi['1']['voda'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck522">
                                    <label class="form-check-label" for="exampleCheck522">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[2][voda]" <?=($analogi['2']['voda'] == 0 ? "checked" : "") ?> value="0" id="exampleCheck531">
                                    <label class="form-check-label" for="exampleCheck531">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[2][voda]" <?=($analogi['2']['voda'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck532">
                                    <label class="form-check-label" for="exampleCheck532">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[3][voda]" <?=($analogi['3']['voda'] == 0 ? "checked" : "") ?> value="0" id="exampleCheck541">
                                    <label class="form-check-label" for="exampleCheck541">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[3][voda]" <?=($analogi['3']['voda'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck542">
                                    <label class="form-check-label" for="exampleCheck542">немає</label> 
                                </td>
                            </tr>                        
                            <tr class="text-center">
                                <th class="text-left">Опалення (індивідуальне)</th>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[0][opalen]" <?=($analogi['0']['opalen'] == 0 ? "checked" : "") ?> value="0" id="exampleCheck611">
                                    <label class="form-check-label" for="exampleCheck611">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[0][opalen]" <?=($analogi['0']['opalen'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck612">
                                    <label class="form-check-label" for="exampleCheck612">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[1][opalen]" <?=($analogi['1']['opalen'] == 0 ? "checked" : "") ?> checked value="0" id="exampleCheck621">
                                    <label class="form-check-label" for="exampleCheck621">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[1][opalen]" <?=($analogi['1']['opalen'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck622">
                                    <label class="form-check-label" for="exampleCheck622">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[2][opalen]" <?=($analogi['2']['opalen'] == 0 ? "checked" : "") ?> checked value="0" id="exampleCheck631">
                                    <label class="form-check-label" for="exampleCheck631">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[2][opalen]" <?=($analogi['2']['opalen'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck632">
                                    <label class="form-check-label" for="exampleCheck632">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[3][opalen]" <?=($analogi['3']['opalen'] == 0 ? "checked" : "") ?> checked value="0" id="exampleCheck641">
                                    <label class="form-check-label" for="exampleCheck641">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[3][opalen]" <?=($analogi['3']['opalen'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck642">
                                    <label class="form-check-label" for="exampleCheck642">немає</label> 
                                </td>
                            </tr>                        
                            <tr class="text-center">
                                <th class="text-left">Каналізація</th>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[0][kanal]" <?=($analogi['0']['kanal'] == 0 ? "checked" : "") ?> checked value="0" id="exampleCheck711">
                                    <label class="form-check-label" for="exampleCheck711">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[0][kanal]" <?=($analogi['0']['kanal'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck712">
                                    <label class="form-check-label" for="exampleCheck712">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[1][kanal]" <?=($analogi['1']['kanal'] == 0 ? "checked" : "") ?> checked value="0" id="exampleCheck721">
                                    <label class="form-check-label" for="exampleCheck721">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[1][kanal]" <?=($analogi['1']['kanal'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck722">
                                    <label class="form-check-label" for="exampleCheck722">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[2][kanal]" <?=($analogi['2']['kanal'] == 0 ? "checked" : "") ?> checked value="0" id="exampleCheck731">
                                    <label class="form-check-label" for="exampleCheck731">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[2][kanal]" <?=($analogi['2']['kanal'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck732">
                                    <label class="form-check-label" for="exampleCheck732">немає</label> 
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="analogi[3][kanal]" <?=($analogi['3']['kanal'] == 0 ? "checked" : "") ?> checked value="0" id="exampleCheck741">
                                    <label class="form-check-label" for="exampleCheck741">є</label> 
                                    &nbsp; / &nbsp;
                                    <input type="radio" class="form-check-input" name="analogi[3][kanal]" <?=($analogi['3']['kanal'] == 1 ? "checked" : "") ?> value="1" id="exampleCheck742">
                                    <label class="form-check-label" for="exampleCheck742">немає</label> 
                                </td>
                            </tr>                        

                            <tr>
                                <th>Коефіцієнт коригування</th>
                                <td rowspan="2">&nbsp;</td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="1" data-nomer="11" step="0.01"  name="analogi[1][koff12]"  value="<?=((float)$analogi['1']['koff12'] <= 0 ? '1' : $analogi['1']['koff12'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="2" data-nomer="11" step="0.01"  name="analogi[2][koff12]"  value="<?=((float)$analogi['2']['koff12'] <= 0 ? '1' : $analogi['2']['koff12'])?>"> </td>
                                <td><input type="number"  step="0.01" class="form-control form-control-sm js-calc_big" data-analog="3" data-nomer="11" step="0.01"  name="analogi[3][koff12]"  value="<?=((float)$analogi['3']['koff12'] <= 0 ? '1' : $analogi['3']['koff12'])?>"> </td>
                            </tr>
                            <tr class="js-tr_calc_price_12">
                                <th>Скоригована ціна за 1 кв. м</th>
                                <td><input type="number" readonly class="form-control form-control-sm js-last_price js-last_price_1 text-right" data-analog="1"  name="analogi[1][kk12]"  value="<?=round($analogi['1']['kk12'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm js-last_price js-last_price_2 text-right" data-analog="2"  name="analogi[2][kk12]"  value="<?=round($analogi['2']['kk12'],2)?>"> </td>
                                <td><input type="number" readonly class="form-control form-control-sm js-last_price js-last_price_3 text-right" data-analog="3"  name="analogi[3][kk12]"  value="<?=round($analogi['3']['kk12'],2)?>"> </td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">Статистична обробка одержаних показників</th>
                            </tr>
                            <tr>
                                <th><input type="number" readonly  step="0.01" class="form-control form-control-sm js-sered_last"  name="raschet[ti]" step="0.01"  value="<?=$raschet['ti']?>"> </th>
                                <td>ti</td>
                                <td><input type="number" readonly  step="0.01" class="form-control form-control-sm js-sered_last_1" name="raschet[ti_1]"  value="<?=$raschet['ti_1']?>"> </td>
                                <td><input type="number" readonly  step="0.01" class="form-control form-control-sm js-sered_last_2" name="raschet[ti_2]"  value="<?=$raschet['ti_2']?>"> </td>
                                <td><input type="number" readonly  step="0.01" class="form-control form-control-sm js-sered_last_3" name="raschet[ti_3]"  value="<?=$raschet['ti_3']?>"> </td>
                            </tr>
                            <tr>
                                <th><input type="number" readonly  step="0.01" class="form-control form-control-sm js-ti2" name="raschet[ti2]"  value="<?=$raschet['ti2']?>"> </th>
                                <td>ti2</td>
                                <td><input type="number" readonly  step="0.01" class="form-control form-control-sm js-ti2_1" name="raschet[ti2_1]"  value="<?=$raschet['ti2_1']?>"> </td>
                                <td><input type="number" readonly  step="0.01" class="form-control form-control-sm js-ti2_2" name="raschet[ti2_2]"  value="<?=$raschet['ti2_2']?>"> </td>
                                <td><input type="number" readonly  step="0.01" class="form-control form-control-sm js-ti2_3" name="raschet[ti2_3]"  value="<?=$raschet['ti2_3']?>"> </td>
                            </tr>
                            <tr>
                                <th colspan="2">Коефіцієнт варіації (V)</th>
                                <td>%</td>
                                <td>&nbsp;</td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-koff_v" name="raschet[koff_v]"  value="<?=$raschet['koff_v']?>"> </td>
                            </tr>
                            <tr>
                                <th class="table-active" colspan="5" style="text-align: center">Статистична обробка одержаних показників</th>
                            </tr>
                            <tr>
                                <th colspan="2">Середнє арифметичне значення 1 кв. м</th>
                                <td>грн. / 1 кв. м</td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-sered_last"  name="raschet[ti]"  value="<?=$raschet['ti']?>"></td>
                                <td><input type="number" readonly step="0.01" class="form-control form-control-sm js-full_price"  name="raschet[full_price]"  value="<?=$raschet['full_price']?>"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="collapse" id="tabf"  data-parent="#accordionExample">
                    <div class="card-header d-flex align-items-center">
                        <h4>Файли справи</h4>
                    </div>                
                    <div class="card-body">
                        <?php 
                            foreach ($mainoFile as $key => $value) {
                                echo '<div class="row js-rowfiles">';
                                switch ($value->type) {
                                    case 't':
                                        echo '<div class="col-2">Технічна документація</div>';
                                        break;
                                    case 'b':
                                        echo '<div class="col-2">Бух.Дані</div>';
                                        break;
                                    case 'f':
                                        echo '<div class="col-2">Фото</div>';
                                        break;
                                    case 'y':
                                        echo '<div class="col-2">Установчи</div>';
                                        break;
                                    case 'z':
                                        echo '<div class="col-2">Різне</div>';
                                        break;
                                    case 'a':
                                        echo '<div class="col-2">Аналоги</div>';
                                        break;
                                    default:
                                        echo '';
                                        break;
                                };
                                echo '<div class="col"><a href="'.$value->file_pach.'">'.$value->name.'</a></div>';
                                echo '<div class="col-1"><button type="button" class="close js-to-delete-file" aria-label="Close" data-idfile="'.$value->id.'">
                                        <span aria-hidden="true"  data-idfile="'.$value->id.'">&times;</span>
                                    </button></div>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                    <div class="card-header d-flex align-items-center">
                        <h4>Завантажити файл справи</h4>
                    </div>                
                    <div class="card-body">                    
                        <div class="row">
                            <div class="col-2">
                                <select class="form-control" name="files_type" >
                                    <option value="z">Різне</option>
                                    <option value="a">Аналоги</option>
                                    <option value="y">Установчи</option>
                                    <option value="f">Фото</option>
                                    <option value="b">Бух.Дані</option>
                                    <option value="t">Технічна документація</option>
                                </select>
                            </div>
                            <div class="col">
                                <div class="input-group mb-3 input-group-sm">
                                    <div class="custom-file">
                                        <input type="file" multiple class="custom-file-input js-file-input" name="files[]" id="inputGroupFile" >
                                        <label class="custom-file-label" for="inputGroupFile">Выбрать файл</label>
                                    </div>
                                </div>  
                            </div>
                        </div>                       
                    </div>
                                       
                </div>
            </div>
        </div>
    </div>

    <div class="nav-box nav-box_float" style="left: 200px; margin-left: 0px;">
        <div class="row justify-content-end text-center">
            <div class="col">
                <div class="form-group row" style="margin-bottom: 0px;">
                    <label class="col-sm-4 form-control-label" style="margin-bottom: 0px;">USD</label>
                    <div class="col-sm-8" style="margin-bottom: 0px;">
                        <input type="text" readonly id="val_curr" class="form-control form-control-sm" value="<?=$valCurUSD->rate;?>">
                    </div>
                </div>            
            </div>
            <div class="col">
                <div class="form-group row" style="margin-bottom: 0px;">
                    <label class="col-sm-4 form-control-label" style="margin-bottom: 0px;">EUR</label>
                    <div class="col-sm-8" style="margin-bottom: 0px;">
                        <input type="text" readonly id="val_curr" class="form-control form-control-sm" value="<?=$valCurEUR->rate;?>">
                    </div>
                </div>            
            </div>
            <div class="col">
                <div class="form-group row" style="margin-bottom: 0px;">
                    <label class="col-sm-4 form-control-label" style="margin-bottom: 0px;">RUB</label>
                    <div class="col-sm-8" style="margin-bottom: 0px;">
                        <input type="text" readonly id="val_curr" class="form-control form-control-sm" value="<?=$valCurRUB->rate;?>">
                    </div>
                </div>            
            </div>                        
            <div class='col-12 col-sm-3 col-md-2 col-lg-2'>
                <input type="submit" value="Зберегти"  class="btn btn-success btn-sm w-100">
            </div>
            <div class='col-12 col-sm-3 col-md-2 col-lg-2'>
                <a href="/property-rights-apartment/" class="btn btn-danger btn-sm w-100">Вихід</a>
            </div>
        </div>
    </div>
</form>
<style>
    input[type=number] { 
        text-align: right; 
    }
</style>

<script>
    window.onload = function() {
        window.analizrinky = CKEDITOR.replace( 'analizrinky' );
        window.tehbud = CKEDITOR.replace( 'tehbud' );
    };
    $('#savepropert').submit(function(){
        // console.log(window.analizrinky.getData());
        $('#analizrinky_name').val(window.analizrinky.getData());
        $('#tehbud_name').val(window.tehbud.getData());
    });
</script>
<script src="/wp-content/themes/nazaret/template/property-rights-apartment/script.js"></script>