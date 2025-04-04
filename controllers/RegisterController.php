<?php

session_start();
include_once "../models/RegisterModel.php";

class RegisterController
{
    public $tipo_documento;
    public $documento;
    public $nombre;
    public $apellido;
    public $fecha_nacimiento;
    public $genero;
    public $email;
    public $password;

    public function ctrRegister()
    {
        $respuesta = RegisterModel::mdlRegister(
            $this->tipo_documento,
            $this->documento,
            $this->nombre,
            $this->apellido,
            $this->fecha_nacimiento,
            $this->genero,
            $this->email,
            $this->password
        );

        if ($respuesta["codigo"] === 200) {
            header("Location: ../views/login.php?msg=" . urlencode($respuesta["mensaje"]) . "&status=success");
        } else {
            header("Location: ../views/register.php?msg=" . urlencode($respuesta["mensaje"]) . "&status=error");
        }
        exit();
    }
}

// Verificar si se envió el formulario de registro
if (isset($_POST["nombre"]) && isset($_POST["apellido"]) && isset($_POST["email"]) && isset($_POST["password"])) {
    $objRegister = new RegisterController();
    $objRegister->tipo_documento = $_POST["tipo_documento"];
    $objRegister->documento = $_POST["documento"];
    $objRegister->nombre = $_POST["nombre"];
    $objRegister->apellido = $_POST["apellido"];
    $objRegister->fecha_nacimiento = $_POST["fecha_nacimiento"];
    $objRegister->genero = $_POST["genero"];
    $objRegister->email = $_POST["email"];
    $objRegister->password = $_POST["password"];
    $objRegister->ctrRegister();
}

