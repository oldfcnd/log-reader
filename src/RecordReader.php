<?php

namespace Fcno\LogReader;

use Bcremer\LineReader\LineReader;
use Fcno\LogReader\Contracts\{BaseContentReader, IPaginate};
use Illuminate\Support\Collection;

/**
 * Manipular um arquivo de log para extrair seus registros completos.
 *
 * Informações contidas em cada registro:
 * - date    - data do evento
 * - time    - hora do evento
 * - env     - ambiente em que o evento ocorreu
 * - level   - nível do evento nos termos da PSR-3
 * - message - mensagem
 * - context - mensagem de contexto
 * - extra   - dados extras sobre o evento
 *
 * @author Fábio Cassiano <fabiocassiano@jfes.jus.br>
 */
final class RecordReader extends BaseContentReader implements IPaginate
{
    /**
     * @var int
     *
     * Página da paginação que será exibida quando o resultado for paginado.
     */
    private $page;

    /**
     * @var int
     *
     * Quantidada de registros por página que serão exibidos quando da paginação.
     */
    private $per_page;

    /**
     * @inheritdoc
     *
     * Nesse caso, os registros do arquivo de log.
     */
    public function get(): Collection
    {
        return $this->readLog();
    }

    /**
     *@inheritdoc
     */
    public function paginate(int $page, int $per_page): Collection
    {
        throw_if($page < 1 || $per_page < 1);

        $this->page     = $page;
        $this->per_page = $per_page;

        return $this->readLog();
    }

    /**
     * @inheritdoc
     */
    protected function getLineGenerator(): \LimitIterator|\Generator
    {
        $line_generator = LineReader::readLines($this->getFullPath());

        return ($this->page && $this->per_page)
        ? new \LimitIterator(
            iterator: $line_generator,
            offset: ($this->page - 1) * $this->per_page,
            limit: $this->per_page
        )
        : $line_generator;
    }

    /**
     * @inheritdoc
     *
     * Interesse em:
     * - date
     * - time
     * - env
     * - level
     * - message
     * - context
     * - extra
     */
    protected function filteredData(array $data): Collection
    {
        return collect($data)
                ->only(['date', 'time', 'env', 'level', 'message', 'context', 'extra']);
    }
}
