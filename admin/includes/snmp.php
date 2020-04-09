<?php 
include 'config/db.php';
include 'funciones.php';
if(isset($_POST['accion']) && $_POST['accion'] == 'add'){
    if(isset($_POST['version']) && $_POST['version'] == '3'){
        if(isset($_POST['nombre']) && isset($_POST['community_string']) && isset($_POST['seguridad'])){  
            $version         = "SNMPv3";
            $nombre          = limpia($_POST['nombre']);
            $comunidad       = limpia($_POST['community_string']);  
            if($_POST['seguridad'] == '1'){                 
                $nivel_seguridad = "noAuthNoPriv";
                consulta($conexion, "INSERT INTO comunidades (id,nombre,version,usuario,nivel_seguridad) VALUES('','$nombre','$version','$comunidad','$nivel_seguridad')");
            }elseif($_POST['seguridad'] == '2' && isset($_POST['auth_protocol']) && isset($_POST['auth_pass'])){
                $nivel_seguridad = "authNoPriv";
                $auth_pass       = openssl_encrypt(limpia($_POST['auth_pass']), 'aes128', $pass_cifrado);
                if($_POST['auth_protocol'] == '1'){
                    $auth_protocol   = "MD5";
                    consulta($conexion, "INSERT INTO comunidades (id,nombre,version,usuario,nivel_seguridad,protocolo_autenticacion,pass_autenticacion)
                    VALUES('','$nombre','$version','$comunidad','$nivel_seguridad','$auth_protocol','$auth_pass')");
                }elseif($_POST['auth_protocol'] == '2'){
                    $auth_protocol   = "SHA";
                    consulta($conexion, "INSERT INTO comunidades (id,nombre,version,usuario,nivel_seguridad,protocolo_autenticacion,pass_autenticacion)
                    VALUES('','$nombre','$version','$comunidad','$nivel_seguridad','$auth_protocol','$auth_pass')");

                }else{
                    echo '<script>alert("Protocolo De Autenticación Incorrecto")</script>';
                }

            }elseif($_POST['seguridad'] == 3 && isset($_POST['auth_protocol']) && isset($_POST['auth_pass'])
             && isset($_POST['priv_protocol']) && isset($_POST['priv_pass'])){
                $nivel_seguridad = "authNoPriv";
                $auth_pass       = openssl_encrypt(limpia($_POST['auth_pass']), 'aes128', $pass_cifrado);
                $auth_priv       = openssl_encrypt(limpia($_POST['auth_priv']), 'aes128', $pass_cifrado);
                if($_POST['auth_protocol'] == '1'){
                    $auth_protocol   = "MD5";
                }elseif($_POST['auth_protocol'] == '2'){
                    $auth_protocol   = "SHA";
                }else{
                    echo '<script>alert("Protocolo De Autenticación Incorrecto")</script>';
                }
                if($_POST['priv_protocol'] == '1'){
                    $auth_protocol   = "DES";
                    consulta($conexion, "INSERT INTO comunidades  VALUES('','$nombre','$version','$comunidad','$nivel_seguridad','$auth_protocol','$auth_pass','$priv_protocol','$pass_protocol')");
                }elseif($_POST['priv_protocol'] == '2'){
                    $auth_protocol   = "AES";
                    consulta($conexion, "INSERT INTO comunidades VALUES('','$nombre','$version','$comunidad','$nivel_seguridad','$auth_protocol','$auth_pass','$priv_protocol','$pass_protocol')");
                }else{
                    echo '<script>alert("Protocolo De Privacidad Incorrecto")</script>';
                }

            }else{
                echo '<script>alert("Nivel De Seguridad Incorrecto")</script>';
            }
            
        } else {
            echo '<script>alert("Faltan Datos")</script>';
        }

    } elseif(isset($_POST['version']) && $_POST['version'] == '1' || $_POST['version'] == '2' ){
        if(isset($_POST['nombre']) && isset($_POST['community_string'])){
            $version   = ($_POST['version'] == '2') ? "SNMPv2c" : "SNMPv1";
            $nombre    = limpia($_POST['nombre']);
            $comunidad = limpia($_POST['community_string']);
            consulta($conexion, "INSERT INTO comunidades (id,nombre,version,usuario) VALUES('','$nombre','$version','$comunidad')");
        }
    } else{
        echo '<script>alert("Version SNMP Incorrecta")</script>';
    }

}elseif (isset($_POST['accion']) && $_POST['accion'] == 'borrar' && isset($_POST['id'])){
    $id = limpia($_POST['id']);
    consulta($conexion,"DELETE FROM comunidades WHERE id=$id;");
    consulta($conexion,"DELETE FROM comunidades_equipos WHERE id_comunidad=$id;");
}
?>
<h1>Comunidades SNMP</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Version</th>
        <th>Community string</th>
    </tr>
    <?php
        $lista_comunidades = consulta($conexion, "SELECT id,nombre,version,usuario FROM comunidades");
        if($lista_comunidades){
            foreach($lista_comunidades as $value){
                echo "<tr>";
                echo "<td>$value[0]</td>";
                echo "<td>$value[1]</td>";
                echo "<td>$value[2]</td>";
                echo "<td>$value[3]</td>";
                echo "</tr>";
            }
        }else{
            echo "no hay comunidades";
        }
        
    ?>
    </table>
    <div class="add_comunidad">
    <div>
    <h3 style="float:left;width:90%">Añadir comunidad</h4><h4 id="mas" style="float:right;text-align:right; margin-right:5%;width:5%">+</h4>
    </div>
    <form class="add_comunidad_formulario" action="index.php" method="POST">
        Nombre<br> 
        <input type="text" name="nombre" required><br>
        Version<br> 
        <select class="version" name="version" required> <option value="1">SNMPv1</option><option value="2">SNMPv2c</option><option value="3">SNMPv3 </option></select><br>
        Comunidad<br> 
        <input type="text" name="community_string" required>
        <span class="seguridad">Nivel De Seguridad</span>
        <select class="seguridad" name="seguridad"> <option value="1">noAuthNoPriv</option><option value="2">authNoPriv</option><option value="3">authPriv</option></select>
        <span class="autenticacion">Protocolo de autenticación</span>
        <select class="autenticacion" name="auth_protocol"> <option value="1">MD5</option><option value="2">SHA</option></select>
        <span class="autenticacion">Contraseña autenticación</span>
        <input class="autenticacion" type="password" name="auth_pass">
        <span class="privacidad">Protocolo de privacidad</span>
        <select class="privacidad" name="priv_protocol"> <option value="1">DES</option><option value="2">AES</option></select>
        <span class="privacidad">Contraseña privacidad</span>
        <input class="privacidad" type="password" name="priv_pass">
        <input type="hidden" name="servicio" value="snmp"><br>
        <input type="hidden" name="accion" value="add"><br>
        <input type="submit">
    </form>
    </div>
    <div class="eliminar_comunidad">
    <div>
    <h3 style="float:left;width:90%">Eliminar comunidad</h4><h4 id="mas2" style="float:right;text-align:right; margin-right:5%;width:5%">+</h4>
    </div>
    <form class="eliminar_comunidad_formulario" action="index.php" method="POST">
        ID de comunidad<br> 
        <input type="text" name="id" required><br>
        <input type="hidden" name="servicio" value="snmp"><br>
        <input type="hidden" name="accion" value="borrar"><br>
        <input type="submit">
    </form>
    </div>
    <script src="js/jquery.js"></script>
    <script>
        $(".add_comunidad div").on("click",function(){
            $(".add_comunidad_formulario").toggle();
            if(document.getElementById("mas").innerHTML == "+"){
                document.getElementById("mas").innerHTML = "-"
            }else{
                document.getElementById("mas").innerHTML = "+"
            }
        });
        $(".eliminar_comunidad div").on("click",function(){
            $(".eliminar_comunidad_formulario").toggle();
            if(document.getElementById("mas2").innerHTML == "+"){
                document.getElementById("mas2").innerHTML = "-"
            }else{
                document.getElementById("mas2").innerHTML = "+"
            }
        });
        $(document).ready(function(){
            $("select.version").change(function(){
                var version_elegida = $(this).children("option:selected").val();
                if (version_elegida == 3) {
                    $(".seguridad").css("display", "block");
                    $("input.seguridad").prop('required',true);
                } else{
                    $(".seguridad").css("display", "none");
                    $(".autenticacion").css("display", "none");
                    $(".privacidad").css("display", "none");
                    $("input.seguridad").prop('required',false);
                    $("input.autenticacion").prop('required',false);
                    $("input.privacidad").prop('required',false);
                }
            });
            $("select.seguridad").change(function(){
                var seguridad = $(this).children("option:selected").val();
                if (seguridad == 2) {
                    $(".autenticacion").css("display", "block");
                    $(".privacidad").css("display", "none");
                    $("input.autenticacion").prop('required',true);
                    $("input.privacidad").prop('required',false);
                } else if (seguridad == 3){
                    $(".autenticacion").css("display", "block");
                    $(".privacidad").css("display", "block");
                    $("input.autenticacion").prop('required',true);
                    $("input.privacidad").prop('required',true);
                } else {
                    $(".autenticacion").css("display", "none");
                    $(".privacidad").css("display", "none");
                    $("input.autenticacion").prop('required',false);
                    $("input.privacidad").prop('required',false);
                }
            });
        });
    </script>
