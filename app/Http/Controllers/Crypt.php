<?php

namespace App\Http\Controllers;

/**
 * Essa classe realiza criptografia simétrica utilizando a biblioteca mcrypt.
 *
 * Recebe uma chave no construtor para criptografar e decriptografar.
 */
class Crypt
{
    /**
     * Chave para criptografar e decriptografar.
     *
     * @var string
     */
    private  $key;
    private $nonce;

    public function __construct()
    {
        $this->key = env('CRYPT_KEY');
        $this->nonce = env('CRYPT_NONCE');
    }

    /**
     * Realiza a encryptação de texto simples utilizando o algoritmo AES com 256 bits,
     * e uma chave gerada à partir de um hash md5 (128 bits) de uma senha fixa e secreta.
     * Posteriormente empacota o conteúdo gerado através da criptografia para transporte HTTP,
     * através das funções base64_encode e rawurlencode().
     *
     * @param  string $plainText Conteúdo que será criptografado.
     * @return string            Conteúdo criptografado.
     */
    public function encrypt($plainText)
    {
        return rawurlencode(base64_encode(sodium_crypto_secretbox($plainText, hex2bin($this->nonce), hex2bin($this->key))));
    }


    /**
     * Realiza o processo inverso o da função de encryptação.
     * Recebe um texto codificado para transporte e decodifica-o, obtendo o conteúdo criptografado.
     * Este conteúdo é então decryptografado, utilizando o algoritmo de decryptação correspondente (AES256).
     * A chave criptográfica é obtida através do hash da mesma senha que encryptou o texto.
     *
     * @param  string $encryptedText Conteúdo criptografado.
     * @return string                Conteúdo decriptografado.
     */
    public function decrypt($encryptedText)
    {
        return trim( sodium_crypto_secretbox_open ( base64_decode(rawurldecode( $encryptedText )  ), hex2bin($this->nonce), hex2bin($this->key) ) );
    }

}

