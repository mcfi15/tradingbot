@extends('templates.york.errors.layout')

@section('title', __('500 - Server Error'))
@section('code', '500')
@section('message', __('Internal Server Error'))
@section('description', __('An unexpected error occurred on our servers. Our team has been notified and is looking into it.'))
