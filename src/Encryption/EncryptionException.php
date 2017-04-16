<?php

namespace EntWeChat\Encryption;

use EntWeChat\Core\Exception as CoreException;

/**
 * Class EncryptionException.
 */
class EncryptionException extends CoreException
{
    const ERROR_INVALID_SIGNATURE = -40001; // Signature verification failed
    const ERROR_PARSE_XML         = -40002; // Parse XML failed
    const ERROR_CALC_SIGNATURE    = -40003; // Calculating the signature failed
    const ERROR_INVALID_AESKEY    = -40004; // Invalid AESKey
    const ERROR_INVALID_CORPID    = -40005; // Check CorpID failed
    const ERROR_ENCRYPT_AES       = -40006; // AES Encryption failed
    const ERROR_DECRYPT_AES       = -40007; // AES decryption failed
    const ERROR_INVALID_XML       = -40008; // Invalid XML
    const ERROR_BASE64_ENCODE     = -40009; // Base64 encoding failed
    const ERROR_BASE64_DECODE     = -40010; // Base64 decoding failed
    const ERROR_XML_BUILD         = -40011; // XML build failed
    const ILLEGAL_BUFFER          = -41003; // Illegal buffer
}
