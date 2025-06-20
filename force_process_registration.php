<?php
/**
 * Script para forzar el procesamiento de una registración específica
 * Colocar este archivo en la raíz del sitio WordPress y ejecutarlo desde el navegador
 */

// Verificar que estamos en WordPress
if (!defined('ABSPATH')) {
    require_once('wp-config.php');
}

// Verificar permisos de administrador
if (!current_user_can('manage_options')) {
    wp_die('Acceso denegado');
}

echo '<h1>Forzar Procesamiento de Registración - Usuario 5992</h1>';

$target_user_id = 5992;

// Verificar si el usuario existe
$user = get_userdata($target_user_id);
if (!$user) {
    echo '<p style="color: red;">Usuario ID ' . $target_user_id . ' no encontrado.</p>';
    exit;
}

echo '<h2>Información del Usuario</h2>';
echo '<div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">';
echo '<strong>ID:</strong> ' . $user->ID . '<br>';
echo '<strong>Nombre:</strong> ' . $user->display_name . '<br>';
echo '<strong>Email:</strong> ' . $user->user_email . '<br>';
echo '<strong>Roles:</strong> ' . implode(', ', $user->roles) . '<br>';
echo '</div>';

// Verificar registraciones pendientes actuales
echo '<h2>Registraciones Pendientes Actuales</h2>';
$pending_registrations = get_option('mailpn_pending_welcome_registrations', []);
echo '<pre>';
print_r($pending_registrations);
echo '</pre>';

// Botón para forzar el procesamiento
echo '<h2>Acciones</h2>';
echo '<form method="post">';
echo '<input type="submit" name="force_process" value="Forzar Procesamiento del Usuario ' . $target_user_id . '" style="background: #d63638; color: white; padding: 10px 20px; border: none; cursor: pointer;">';
echo '</form>';

// Procesar si se solicita
if (isset($_POST['force_process'])) {
    echo '<h3>Procesando registración forzada...</h3>';
    
    // Crear una nueva instancia de settings
    $settings_plugin = new MAILPN_Settings();
    
    // Forzar el envío de correos de bienvenida para este usuario
    $sent = $settings_plugin->mailpn_trigger_welcome_emails($target_user_id);
    
    if ($sent) {
        echo '<p style="color: green;">✅ Correo de bienvenida enviado exitosamente.</p>';
        
        // Actualizar la registración pendiente para marcarla como procesada
        $updated_pending_registrations = [];
        foreach ($pending_registrations as $registration) {
            if ($registration['user_id'] == $target_user_id) {
                $registration['processed'] = true;
            }
            $updated_pending_registrations[] = $registration;
        }
        update_option('mailpn_pending_welcome_registrations', $updated_pending_registrations);
        
        echo '<p>Registración marcada como procesada.</p>';
    } else {
        echo '<p style="color: orange;">⚠️ No se pudo enviar el correo de bienvenida. Posibles causas:</p>';
        echo '<ul>';
        echo '<li>No hay plantillas de correo de bienvenida configuradas</li>';
        echo '<li>El usuario no cumple con los criterios de distribución de las plantillas</li>';
        echo '<li>Problemas de configuración del sistema de correo</li>';
        echo '</ul>';
    }
    
    echo '<p><a href="">Recargar página</a></p>';
}

// Verificar plantillas de correo de bienvenida
echo '<h2>Plantillas de Correo de Bienvenida Disponibles</h2>';
$welcome_emails = get_posts([
    'fields' => 'ids',
    'numberposts' => -1,
    'post_type' => 'mailpn_mail',
    'post_status' => 'publish',
    'meta_query' => [
        [
            'key' => 'mailpn_type',
            'value' => 'email_welcome',
            'compare' => '='
        ]
    ]
]);

if (empty($welcome_emails)) {
    echo '<p style="color: red;">No hay plantillas de correo de bienvenida configuradas.</p>';
    echo '<p>Para que funcione el sistema de correos de bienvenida, necesitas crear al menos una plantilla de correo con el tipo "Welcome email".</p>';
} else {
    echo '<p>Plantillas encontradas: ' . count($welcome_emails) . '</p>';
    foreach ($welcome_emails as $email_id) {
        $email_post = get_post($email_id);
        $distribution = get_post_meta($email_id, 'mailpn_distribution', true);
        
        echo '<div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">';
        echo '<strong>ID:</strong> ' . $email_id . '<br>';
        echo '<strong>Título:</strong> ' . $email_post->post_title . '<br>';
        echo '<strong>Distribución:</strong> ' . $distribution . '<br>';
        
        // Verificar si este usuario debería recibir este correo
        $should_receive = false;
        switch ($distribution) {
            case 'public':
                $should_receive = true;
                break;
            case 'private_role':
                $user_roles = get_post_meta($email_id, 'mailpn_distribution_role', true);
                if (!empty($user_roles)) {
                    foreach ($user_roles as $role) {
                        if (in_array($role, $user->roles)) {
                            $should_receive = true;
                            break;
                        }
                    }
                }
                break;
            case 'private_user':
                $user_list = get_post_meta($email_id, 'mailpn_distribution_user', true);
                if (!empty($user_list) && in_array($target_user_id, $user_list)) {
                    $should_receive = true;
                }
                break;
        }
        
        echo '<strong>Usuario debería recibir:</strong> ' . ($should_receive ? 'Sí' : 'No') . '<br>';
        echo '</div>';
    }
}

echo '<hr>';
echo '<p><small>Script de forzado de procesamiento - MAILPN Plugin</small></p>';
?> 