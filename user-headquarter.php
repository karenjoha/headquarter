<?php
/*
Plugin Name: Campo Personalizado Sede para Usuarios
Description: Añade un campo personalizado "Sede" en el perfil de usuario.
Version: 1.0
Author: Karen Herrera
*/

// Añadir campo personalizado "Sede" en la página de creación y edición de usuarios
function agregar_campo_sede($user) { 
    // Lista de sedes (puedes personalizar esta lista)
    $sedes = array(
        'LAURELES',
        'POBLADO',
        'LOS COLORES',
        'GUARNE',
        'BELLO',
        'SABANETA',
        'ENVIGADO',
        'LA ESTRELLA',
        'RIONEGRO',
        'FONTIBON',
        'MOSQUERA',
        'NORTE DE BOGOTA'
    );

    // Obtener la sede actual del usuario (o vacío si es nuevo usuario)
    $sede_actual = is_object($user) ? get_the_author_meta('sede', $user->ID) : '';
    ?>
    <h3><?php esc_html_e('Información de Sede', 'text-domain'); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="sede"><?php esc_html_e('Sede', 'text-domain'); ?></label></th>
            <td>
                <select name="sede" id="sede" class="regular-text">
                    <option value=""><?php esc_html_e('Selecciona una sede', 'text-domain'); ?></option>
                    <?php foreach ($sedes as $sede) : ?>
                        <option value="<?php echo esc_attr($sede); ?>" <?php selected($sede_actual, $sede); ?>>
                            <?php echo esc_html($sede); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php esc_html_e('Selecciona la sede del usuario', 'text-domain'); ?></p>
            </td>
        </tr>
    </table>
    <?php 
}

// Mostrar el campo en el perfil de usuario
add_action('show_user_profile', 'agregar_campo_sede');
add_action('edit_user_profile', 'agregar_campo_sede');

// Mostrar el campo en la creación de un nuevo usuario
add_action('user_new_form', 'agregar_campo_sede');

// Guardar el valor del campo "Sede"
function guardar_campo_sede($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    
    // Verificar si el campo está presente y guardarlo
    if (isset($_POST['sede'])) {
        update_user_meta($user_id, 'sede', sanitize_text_field($_POST['sede']));
    }
}

// Guardar el campo al actualizar el perfil de usuario
add_action('personal_options_update', 'guardar_campo_sede');
add_action('edit_user_profile_update', 'guardar_campo_sede');

// Guardar el campo al crear un nuevo usuario
add_action('user_register', 'guardar_campo_sede');

// Añadir la columna de "Sede" en la tabla de usuarios
function agregar_columna_sede($columns) {
    $columns['sede'] = __('Sede', 'text-domain');
    return $columns;
}
add_filter('manage_users_columns', 'agregar_columna_sede');

// Mostrar los datos de la columna "Sede"
function mostrar_columna_sede($value, $column_name, $user_id) {
    if ('sede' == $column_name) {
        return get_user_meta($user_id, 'sede', true);
    }
    return $value;
}
add_filter('manage_users_custom_column', 'mostrar_columna_sede', 10, 3);

// Hacer la columna "Sede" ordenable
function columna_sede_ordenable($columns) {
    $columns['sede'] = 'sede';
    return $columns;
}
add_filter('manage_users_sortable_columns', 'columna_sede_ordenable');





// Añadir campo personalizado de imagen en el perfil de usuario
function agregar_campo_imagen($user) {
    $imagen_url = get_user_meta($user->ID, 'user_profile_image', true); // Obtén la URL de la imagen
    ?>
    <h3><?php esc_html_e('Información de Imagen de Perfil', 'text-domain'); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="user_profile_image"><?php esc_html_e('Imagen de Perfil', 'text-domain'); ?></label></th>
            <td>
                <input type="text" name="user_profile_image" id="user_profile_image" value="<?php echo esc_attr($imagen_url); ?>" class="regular-text" />
                <input type="button" class="button button-secondary" value="<?php esc_html_e('Subir Imagen', 'text-domain'); ?>" id="upload_image_button" />
                <p class="description"><?php esc_html_e('Sube la imagen de perfil del usuario', 'text-domain'); ?></p>
            </td>
        </tr>
    </table>

    <script>
        jQuery(document).ready(function($){
            var mediaUploader;
            $('#upload_image_button').click(function(e) {
                e.preventDefault();
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media({
                    title: 'Seleccionar Imagen',
                    button: {
                        text: 'Seleccionar Imagen'
                    },
                    multiple: false 
                });
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#user_profile_image').val(attachment.url);
                });
                mediaUploader.open();
            });
        });
    </script>
    <?php
}

// Mostrar el campo en el perfil de usuario
add_action('show_user_profile', 'agregar_campo_imagen');
add_action('edit_user_profile', 'agregar_campo_imagen');

// Guardar la imagen de perfil
function guardar_campo_imagen($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    
    // Verificar si el campo está presente y guardarlo
    if (isset($_POST['user_profile_image'])) {
        update_user_meta($user_id, 'user_profile_image', sanitize_text_field($_POST['user_profile_image']));
    }
}

// Guardar el campo al actualizar el perfil de usuario
add_action('personal_options_update', 'guardar_campo_imagen');
add_action('edit_user_profile_update', 'guardar_campo_imagen');

// Guardar el campo al crear un nuevo usuario
add_action('user_register', 'guardar_campo_imagen');
