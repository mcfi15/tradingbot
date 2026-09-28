@extends('templates.york.errors.layout')

@section('title', __('401 - Unauthorized'))
@section('code', '401')
@section('message', __('Unauthorized Access'))
@section('description', __('You must be logged in with the appropriate permissions to view this page.'))
