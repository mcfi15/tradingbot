@php
    $template = config('site.template', 'york');
@endphp

@extends('templates.' . $template . '.errors.layout')
