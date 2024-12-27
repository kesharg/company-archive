<?php

return [
  "Dashboard" => [
      'admin_dashboard' => 'admin.dashboard',
  ],

  "notifications" => [
      'all_notifications'         => 'admin.notifications.index',
      'notification_mark_as_read' => 'admin.notifications.markAsRead',
  ],

  "partner" =>[
    'all_partner'     => 'admin.partners.index',
    'add_partner'     => 'admin.partners.create',
    'partner_store'   => 'admin.partners.store',
    'partner_edit'    => 'admin.partners.edit',
    'partner_show'    => 'admin.partners.show',
    'partner_update'  => 'admin.partners.update',
    'partner_delete'  => 'admin.partners.destroy',
  ],
  "distributor" =>[
    'all_distributor'    => 'admin.distributors.index',
    'add_distributor'    => 'admin.distributors.create',
    'distributor_store'  => 'admin.distributors.store',
    'distributor_edit'   => 'admin.distributors.edit',
    'distributor_show'   => 'admin.distributors.show',
    'distributor_update' => 'admin.distributors.update',
    'distributor_delete' => 'admin.distributors.destroy',
  ],
  "feature" => [
    'all_features'    => 'admin.features.index',
    'add_features'    => 'admin.features.create',
    'features_store'  => 'admin.features.store',
    'features_edit'   => 'admin.features.edit',
    'features_show'   => 'admin.features.show',
    'features_update' => 'admin.features.update',
    'features_delete' => 'admin.features.destroy',
  ],

  "feature" => [
    'all_blog'    => 'admin.blogs.index',
    'add_blog'    => 'admin.blogs.create',
    'blog_store'  => 'admin.blogs.store',
    'blog_edit'   => 'admin.blogs.edit',
    'blog_show'   => 'admin.blogs.show',
    'blog_update' => 'admin.blogs.update',
    'blog_delete' => 'admin.blogs.destroy',
  ],

  "models" => [
    'all_model'    => 'admin.models.index',
    'add_model'    => 'admin.models.create',
    'model_store'  => 'admin.models.store',
    'model_edit'   => 'admin.models.edit',
    'model_show'   => 'admin.models.show',
    'model_update' => 'admin.models.update',
    'model_delete' => 'admin.models.destroy',
  ],

  "role" =>[
    'all_role'    => 'admin.roles.index',
    'add_role'    => 'admin.roles.create',
    'role_store'  => 'admin.roles.store',
    'role_edit'   => 'admin.roles.edit',
    'role_show'   => 'admin.roles.show',
    'role_update' => 'admin.roles.update',
    'role_delete' => 'admin.roles.destroy',
  ],


  "user" =>[
    'all_user'    => 'admin.users.index',
    'add_user'    => 'admin.users.create',
    'user_store'  => 'admin.users.store',
    'user_edit'   => 'admin.users.edit',
    'user_show'   => 'admin.users.show',
    'user_update' => 'admin.users.update',
    'user_delete' => 'admin.users.destroy',
  ],


  "package" =>[
    'package_lists'    => 'admin.package.lists',
    'all_package'      => 'admin.package.index',
    'add_package'      => 'admin.package.create',
    'generate_package' => 'admin.package.generate',
    'package_store'    => 'admin.package.store',
    'package_edit'     => 'admin.package.edit',
    'package_show'     => 'admin.package.show',
    'package_update'   => 'admin.package.update',
    'package_delete'   => 'admin.package.destroy',
  ],

  "series" =>[
    'all_series'    => 'admin.series.index',
    'add_series'    => 'admin.series.create',
    'series_store'  => 'admin.series.store',
    'series_edit'   => 'admin.series.edit',
    'series_show'   => 'admin.series.show',
    'series_update' => 'admin.series.update',
    'series_delete' => 'admin.series.destroy',
  ],

  "stores" =>[
    'all_store'    => 'admin.stores.index',
    'add_store'    => 'admin.stores.create',
    'store_store'  => 'admin.stores.store',
    'store_edit'   => 'admin.stores.edit',
    'store_show'   => 'admin.stores.show',
    'store_update' => 'admin.stores.update',
    'store_delete' => 'admin.stores.destroy',
  ],

  "codes" => [
    'all_codes'              => 'admin.code.lists',
    'code_scanned_lists'     => 'admin.code.scanned.lists',
    'generate_code'          => 'admin.code.generate',
    'generate_code_for_each' => 'admin.code.generate.each',
    'code_show'              => 'admin.code.show',

  ],

  "settings" =>[
    'all_user-setting'    => 'admin.user-settings.index',
    'add_user-setting'    => 'admin.user-settings.create',
    'user-setting_store'  => 'admin.user-settings.store',
    'user-setting_edit'   => 'admin.user-settings.edit',
    'user-setting_show'   => 'admin.user-settings.show',
    'user-setting_update' => 'admin.user-settings.update',
    'user-setting_delete' => 'admin.user-settings.destroy',
    'user_profile'        => 'admin.user.profile',
    'user_update'         => 'admin.user.profileUpdate',
    'password_reset'      => 'admin.user.admin.userPasswordReset',
    'password_reset'      => 'admin.user.admin.updatePassword.update',
    'code_zip_download'   => 'admin.code.zip.download',
    'file_download'       => 'admin.file.download',
    'code_export'         => 'admin.code.export',
  ],

];
