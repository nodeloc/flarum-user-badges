<?php

namespace V17Development\FlarumBadges;

use Flarum\Extend;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\User;
use Flarum\Api\Controller as FlarumController;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__ . '/less/Forum.less')
    ,
    // (new Extend\Frontend('admin'))
    //     ->js(__DIR__.'/js/dist/admin.js')
    //     ->css(__DIR__ . '/less/Admin.less'),

    (new Extend\Routes('api'))
        ->get('/badges', 'badges.overview', Api\Controller\ListBadgesController::class)
        ->get('/badge_categories', 'badge.categories.overview', Api\Controller\ListBadgeCategoriesController::class)
        ->get('/badge_users', 'badge.users.overview', Api\Controller\ListUserBadgesController::class)
    ,

    (new Extend\Model(User::class))
        ->hasMany('userBadges', UserBadge\UserBadge::class, 'user_id')
        ->relationship('userPrimaryBadge', function($user) {
            return $user->hasOne(UserBadge\UserBadge::class, 'user_id', null)->where('is_primary', true);
        }),

    (new Extend\ApiSerializer(UserSerializer::class))
        ->hasMany('userBadges', Api\Serializer\UserBadgeSerializer::class)
        ->hasOne('userPrimaryBadge', Api\Serializer\UserBadgeSerializer::class),

    (new Extend\ApiController(FlarumController\ShowUserController::class))
        ->addInclude(['userBadges', 'userBadges.badge', 'userBadges.badge.category', 'userPrimaryBadge', 'userPrimaryBadge.badge']),

    (new Extend\ApiController(FlarumController\ListUsersController::class))
        ->addInclude(['userBadges', 'userPrimaryBadge', 'userPrimaryBadge.badge']),

    new Extend\Locales(__DIR__ . '/locale'),
];
