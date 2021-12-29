<?php

namespace Fcno\LogReader\Contracts;

/**
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
interface IReader
{
    /**
     * Define o ***File System*** de armazenamento dos logs diários da
     * aplicação de acordo com o nome informado.
     *
     * Trata-se do ***File System*** em que este ***Package*** buscará os
     * arquivos de log.
     *
     * @param string $disk nome do ***File System***
     */
    public function from(string $disk): static;
}
