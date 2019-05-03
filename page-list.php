<?php
if (isset($_GET['delid']) && ($_GET['delid'] != '')) {
    $delid = $_GET['delid'];
    $wpdb->update( 'maino',
        array( 'status' => 'd'),
        array( 'id' => $delid ),
        array( '%s' ),
        array( '%d' )
    );
    echo '<script>window.location="'.$_SERVER['SCRIPT_URI'].'"</script>';
}

$sql = "SELECT maino.*, property.fields, maino.nomber as nomer, reestr.id as rid, reestr.nomber, maino.datework, s_city.name as city, s_bank.name as bank, s_meta.name as meta, s_maino.name as mname,  property.id as pid ".
" FROM `maino` ".
     "LEFT JOIN reestr  ON reestr.id       = maino.reestr_id ".
     "LEFT JOIN s_city  ON reestr.city_id  = s_city.id ".
     "LEFT JOIN s_bank  ON reestr.bank_id  = s_bank.id ".
     "LEFT JOIN s_meta  ON reestr.meta_id  = s_meta.id ".
     "LEFT JOIN s_maino ON maino.vid_id    = s_maino.id ".
     "LEFT JOIN property ON property.maino_id = maino.id AND property.reestr_id = reestr.id ".
     "WHERE NOT reestr.status = 'd' AND NOT maino.status in ('d') AND maino.vid_id = 15 ".
     "ORDER BY rid DESC, `maino`.`status`, `maino`.`nom_o`, `maino`.`nomber` ASC";
$rown = $wpdb->get_results($sql);

$sql = "SELECT maino.*, property.fields, maino.nomber as nomer, reestr_a.id as rid, reestr_a.nomber, maino.datework, s_city.name as city, s_bank.name as bank, s_meta.name as meta, s_maino.name as mname,  property.id as pid ".
" FROM `maino` ".
     "LEFT JOIN reestr_a  ON reestr_a.id  = maino.reestr_id ".
     "LEFT JOIN s_city  ON reestr_a.city_id   = s_city.id ".
     "LEFT JOIN s_bank  ON reestr_a.bank_id    = s_bank.id ".
     "LEFT JOIN s_meta  ON reestr_a.meta_id    = s_meta.id ".
     "LEFT JOIN s_maino ON maino.vid_id    = s_maino.id ".
     "LEFT JOIN property ON property.maino_id = maino.id AND property.reestr_id = reestr_a.id ".
     "WHERE NOT reestr_a.status = 'd' AND NOT maino.status in ('d','w') AND maino.vid_id = 15 ORDER BY rid DESC, `maino`.`nom_o`, `maino`.`nomber` ASC";
$rows = $wpdb->get_results($sql);


$sql = "SELECT maino.*, property.fields, maino.nomber as nomer, reestr_a.id as rid, reestr_a.nomber, maino.datework, s_city.name as city, s_bank.name as bank, s_meta.name as meta, s_maino.name as mname,  property.id as pid ".
" FROM `maino` ".
     "LEFT JOIN reestr_a  ON reestr_a.id  = maino.reestr_id ".
     "LEFT JOIN s_city  ON reestr_a.city_id   = s_city.id ".
     "LEFT JOIN s_bank  ON reestr_a.bank_id    = s_bank.id ".
     "LEFT JOIN s_meta  ON reestr_a.meta_id    = s_meta.id ".
     "LEFT JOIN s_maino ON maino.vid_id    = s_maino.id ".
     "LEFT JOIN property ON property.maino_id = maino.id AND property.reestr_id = reestr_a.id ".
     "WHERE NOT reestr_a.status = 'd' AND NOT maino.status in ('d','n') AND maino.vid_id = 15 ORDER BY rid DESC, `maino`.`nom_o`, `maino`.`nomber` ASC";
$rowsw = $wpdb->get_results($sql);
// echo '<pre>'.print_r($_SESSION, true).'</pre>';
$dog = '';
?>
<div class="table-responsive">
    <table class="table table-hover table-bordered table-sm">
        <thead>
        <tr class="text-center">
            <th>№ Договора</th>
            <th>№ Оцінки</th>
            <th>ДАТА</th>
            <th>Власник</th>
            <th>№ Будинку</th>
            <th>№ Квартири</th>
            <th>Адреса</th>
            <th>Номер ФФБ</th>
            <th>Статус</th>
            <th colspan="3">Дія</th>
        </tr>
        <tr class='table-filters'>
            <td>&nbsp;</td>
            <td><input type="text" class="form-control form-control-sm" size="2" /></td>
            <td><input type="text" class="form-control form-control-sm" size="4" /></td>
            <td><input type="text" class="form-control form-control-sm" size="4" /></td>
            <td><input type="text" class="form-control form-control-sm" size="4" /></td>
            <td><input type="text" class="form-control form-control-sm" size="4" /></td>
            <td>&nbsp;</td>
            <td><input type="text" class="form-control form-control-sm" size="4" /></td>
            <td>&nbsp;</td>
            <td colspan="3">&nbsp;</td>
        </tr>            
        </thead>
        <tbody>
            <?php foreach ($rown as $key => $value) { $fields  = json_decode($value->fields); ?>
            <?php 
                if ($dog != $value->rid) {
                    ?>
                        <tr>
                            <td colspan="12">
                                <button class="btn btn-primary w-100" type="button" data-toggle="collapse" data-target="#collapse<?=$value->rid?>" aria-expanded="true" aria-controls="collapse<?=$value->rid?>">
                                Показать оцінки по договору <?=$value->nomber?>
                                </button>
                            </td>
                        </tr>
                    <?php
                    $dog = $value->rid;
                }
            ?>
            <tr id="collapse<?=$value->rid?>" class="collapse table-data" aria-labelledby="heading<?=$value->rid?>">
                <td class="text-center align-middle"><?=$value->nomber?></td>
                <td class="text-center align-middle"><?=$value->nomer?></td>
                <td class="text-center align-middle"><?=$value->datework?></td>
                <td class="align-middle"><?=str_replace(array('\\'),'',$fields->pib);?></td>
                <td class="text-center align-middle"><?=$fields->budinok?></td>
                <td class="text-center align-middle"><?=$fields->nomber_kv?></td>
                <td><?=$fields->adress?></td>
                <td><?=$fields->dogovir?></td>
                <td class="text-center align-middle">
                    <?if ($value->status == 'n') { echo 'нова'; }?>
                    <?if ($value->status == 'w') { echo 'в роботі'; }?>
                </td>
                <td class="text-center align-middle"><a href="/property-rights-apartment?id=<?=$value->id?>" class="btn btn-success btn-sm"><i class="fa fa-pencil-square-o"></i></a></td>
                <td class="text-center align-middle"><a href="/property-rights-apartment?delid=<?=$value->id?>" class="btn btn-danger btn-sm js-delete-property"><i class="fa fa-trash"></i></a></td>
                <td class="text-center align-middle"><a target="_blank" href="/api/property-rights-apartment/doc_create.php?id=<?=$value->pid;?>" class="btn btn-success btn-sm"><i class="fa fa-print"></i></a></td>
            </tr>
            <?php } ?>       


            <?php foreach ($rows as $key => $value) { $fields  = json_decode($value->fields); ?>
            <?php 
                if ($dog != $value->rid) {
                    ?>
                        <tr>
                            <td colspan="12">
                                <button class="btn btn-primary w-100" type="button" data-toggle="collapse" data-target="#collapse<?=$value->rid?>" aria-expanded="true" aria-controls="collapse<?=$value->rid?>">
                                Показать оцінки по договору <?=$value->nomber?>
                                </button>
                            </td>
                        </tr>
                    <?php
                    $dog = $value->rid;
                }
            ?>
            <tr id="collapse<?=$value->rid?>" class="collapse table-data" aria-labelledby="heading<?=$value->rid?>">
                <td class="text-center align-middle"><?=$value->nomber?></td>
                <td class="text-center align-middle"><?=$value->nomer?></td>
                <td class="text-center align-middle"><?=$value->datework?></td>
                <td class="align-middle"><?=str_replace(array('\\'),'',$fields->pib);?></td>
                <td class="text-center align-middle"><?=$fields->budinok?></td>
                <td class="text-center align-middle"><?=$fields->nomber_kv?></td>
                <td><?=$fields->adress?></td>
                <td><?=$fields->dogovir?></td>
                <td class="text-center align-middle">
                    <?if ($value->status == 'n') { echo 'нова'; }?>
                    <?if ($value->status == 'w') { echo 'в роботі'; }?>
                </td>
                <td class="text-center align-middle"><a href="/property-rights-apartment?id=<?=$value->id?>" class="btn btn-success btn-sm"><i class="fa fa-pencil-square-o"></i></a></td>
                <td class="text-center align-middle"><a href="/property-rights-apartment?delid=<?=$value->id?>" class="btn btn-danger btn-sm js-delete-property"><i class="fa fa-trash"></i></a></td>
                <td class="text-center align-middle"><a target="_blank" href="/api/property-rights-apartment/doc_create.php?id=<?=$value->pid;?>" class="btn btn-success btn-sm"><i class="fa fa-print"></i></a></td>
            </tr>
            <?php } ?>

            <?php foreach ($rowsw as $key => $value) { $fields  = json_decode($value->fields); ?>
            <?php 
                if ($dog != $value->rid) {
                    ?>
                        <tr>
                            <td colspan="12">
                                <button class="btn btn-primary w-100" type="button" data-toggle="collapse" data-target="#collapse<?=$value->rid?>" aria-expanded="true" aria-controls="collapse<?=$value->rid?>">
                                Показать договора <?=$value->nomber?>
                                </button>
                            </td>
                        </tr>
                    <?php
                    $dog = $value->rid;
                }
            ?>
            <tr id="collapse<?=$value->rid?>" class="collapse table-data" aria-labelledby="heading<?=$value->rid?>">
                <td class="text-center align-middle"><?=$value->nomber?></td>
                <td class="text-center align-middle"><?=$value->nomer?></td>
                <td class="text-center align-middle"><?=$value->datework?></td>
                <td class="align-middle"><?=str_replace(array('\\'),'',$fields->pib);?></td>
                <td class="text-center align-middle"><?=$fields->budinok?></td>
                <td class="text-center align-middle"><?=$fields->nomber_kv?></td>
                <td><?=$fields->adress?></td>
                <td><?=$fields->dogovir?></td>
                <td class="text-center align-middle">
                    <?if ($value->status == 'n') { echo 'нова'; }?>
                    <?if ($value->status == 'w') { echo 'в роботі'; }?>
                </td>
                <td class="text-center align-middle"><a href="/property-rights-apartment?id=<?=$value->id?>" class="btn btn-success btn-sm"><i class="fa fa-pencil-square-o"></i></a></td>
                <td class="text-center align-middle"><a href="/property-rights-apartment?delid=<?=$value->id?>" class="btn btn-danger btn-sm js-delete-property"><i class="fa fa-trash"></i></a></td>
                <td class="text-center align-middle"><a target="_blank" href="/api/property-rights-apartment/doc_create.php?id=<?=$value->pid;?>" class="btn btn-success btn-sm"><i class="fa fa-print"></i></a></td>
            </tr>
            <?php } ?>            
        </tbody>
    </table>
</div>
<script src="/wp-content/themes/nazaret/template/property-rights-apartment/script-list.js"></script>