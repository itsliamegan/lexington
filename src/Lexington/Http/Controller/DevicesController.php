<?php declare(strict_types=1);

namespace Lexington\Http\Controller;

use Lexington\Common\Arrays;
use Lexington\Http\RequestData\DeviceRequestData;
use Lexington\Model\Device;
use Lexington\Model\User;
use Vector\Http\Exception\BadRequestException;
use Vector\Http\Request;
use Vector\Http\Response;
use Vector\Http\Session;

final class DevicesController
{
    public static function index()
    {
        $page            = Request::param('p') ?? 1;
        $amount_per_page = Request::param('per') ?? 20;
        $max_page        = Device::maxPage($amount_per_page);
        $devices         = Device::page($amount_per_page, $page);

        $user = User::findOrFail(Session::get('user_id'));
        
        Response::view('devices/index', [
            'title'        => 'Devices',
            'current_user' => $user,
            'search_type'  => 'devices',
            'current_page' => $page,
            'max_page'     => $max_page,
            'per_page'     => $amount_per_page,
            'devices'      => $devices
        ]);
    }

    public static function new()
    {
        $user = User::findOrFail(Session::get('user_id'));

        Response::view('devices/show', [
            'title'        => 'New Device',
            'current_user' => $user,
            'search_type'  => 'devices',
            'is_creation'  => true
        ]);
    }

    public static function create()
    {
        $body = Request::body();
        $data = new DeviceRequestData($body);

        if (! $data->isValid()) {
            throw new BadRequestException('You are missing some required data');
        }

        $device = Device::createFromData($data);
        
        Response::redirect("/lexington/devices/{$device->asset_tag}");
    }

    public static function show($asset_tag)
    {
        $device = Device::show($asset_tag);
        $user   = User::findOrFail(Session::get('user_id'));

        Response::view('devices/show', [
            'title'        => $device->name,
            'current_user' => $user,
            'search_type'  => 'devices',
            'is_creation'  => false,
            'device'       => $device
        ]);
    }

    public static function update($asset_tag)
    {
        $device = Device::show($asset_tag);
        $body   = Request::body();
        $data   = new DeviceRequestData($body);

        if (! $data->isValid()) {
            throw new BadRequestException('You are missing some required data');
        }

        $device->updateFromData($data);

        Response::redirect("/lexington/devices/{$device->asset_tag}");
    }

    public static function search()
    {
        $query   = Request::param('q');
        $devices = Device::search($query);
        $user    = User::findOrFail(Session::get('user_id'));

        Response::view('devices/search', [
            'title'        => "\"$query\" : Devices",
            'current_user' => $user,
            'search_type'  => 'devices',
            'search_query' => $query,
            'devices'      => $devices
        ]);
    }

    public static function searchJson()
    {
        $query   = Request::param('q');
        $devices = Device::search($query);

        Response::json(Arrays::camelcase($devices->toArray()));
    }
}
