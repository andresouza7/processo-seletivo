<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RenomearArquivosParaPdf extends Command
{
    protected $signature = 'arquivos:renomear {pasta}';
    protected $description = 'Renomeia recursivamente arquivos não-PDF segmentando pelo primeiro ponto e adicionando .pdf';

    public function handle()
    {
        $pasta = $this->argument('pasta');

        if (!is_dir($pasta)) {
            $this->error("Diretório inválido: $pasta");
            return 1;
        }

        $arquivos = File::allFiles($pasta); // Recursivo!

        foreach ($arquivos as $arquivo) {
            $nomeOriginal = $arquivo->getFilename();
            $extensao = $arquivo->getExtension();

            if (strtolower($extensao) === 'pdf') {
                continue;
            }

            $partes = explode('.', $nomeOriginal, 2);
            $novoNome = $partes[0] . '.pdf';

            $caminhoAntigo = $arquivo->getPathname();
            $caminhoNovo = $arquivo->getPath() . DIRECTORY_SEPARATOR . $novoNome;

            if (file_exists($caminhoNovo)) {
                $this->warn("Arquivo já existe: $novoNome — pulando...");
                continue;
            }

            rename($caminhoAntigo, $caminhoNovo);
            $this->info("Renomeado: $caminhoAntigo -> $novoNome");
        }

        $this->info('Renomeação concluída recursivamente.');
        return 0;
    }
}
