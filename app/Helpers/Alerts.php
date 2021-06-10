<?php

// return Alert success
if (!function_exists('alert_success')) {
  function alert_success($message, $data = [])
  {
    return [
      'key' => true,
      'title' => 'NEW RECORD CREATED',
      'type' => 'success',
      'color' => 'green',
      'icon' => 'checked',
      'message' => $message,
      'data' => $data
    ];
  }
}

// return Alert success
if (!function_exists('alert_update')) {
  function alert_update($message, $data = [])
  {
    return [
      'key' => true,
      'title' => 'RECORD UPDATED',
      'type' => 'warning',
      'color' => 'yellow',
      'icon' => 'edit',
      'message' => $message,
      'data' => $data
    ];
  }
}

// return Alert delete
if (!function_exists('alert_delete')) {
  function alert_delete($message, $data = [])
  {
    return [
      'key' => true,
      'title' => 'RECORD DELETED',
      'type' => 'danger',
      'color' => 'red',
      'icon' => 'trash',
      'message' => $message,
      'data' => $data
    ];
  }
}

// return Duplicate delete
if (!function_exists('alert_duplicate')) {
  function alert_duplicate($message, $data = [])
  {
    return [
      'key' => true,
      'title' => 'Duplicate',
      'type' => 'error',
      'color' => 'red',
      'icon' => 'times',
      'message' => $message,
      'data' => $data,
      'action'=>'duplicate'
    ];
  }
}

// return Duplicate delete
if (!function_exists('alert_failed_delete')) {
  function alert_failed_delete($message, $data = [])
  {
    return [
      'key' => true,
      'title' => 'Failed to Delete',
      'type' => 'error',
      'color' => 'red',
      'icon' => 'times',
      'message' => $message,
      'data' => $data,
      'action'=>'faile_to_delete'
    ];
  }
}

if (!function_exists('alert_status')) {
  function alert_status($message, $status = "")
  {
    return [
      'key' => true,
      'title' => 'Status',
      'type' => 'success',
      'color' => 'green',
      'icon' => 'checked',
      'message' => $message,
      'status' => $status
    ];
  }
}
