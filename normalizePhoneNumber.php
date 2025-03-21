<?php
function normalizePhoneNumber($phoneNumber)
{
  $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

  if (strlen($phoneNumber) === 12 && substr($phoneNumber, 0, 2) === '91') {
    return '+' . $phoneNumber;
  } elseif (strlen($phoneNumber) === 11 && substr($phoneNumber, 0, 1) === '0') {
    return '+91' . substr($phoneNumber, 1);
  } elseif (strlen($phoneNumber) === 10) {
    return '+91' . $phoneNumber;
  }
  return null;
}
