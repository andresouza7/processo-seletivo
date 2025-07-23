Manual de Estilização dos Painéis Filament

1. Estilização Padrão
A maioria da estilização utiliza os componentes padrão do Filament, com configurações feitas através dos métodos disponíveis na biblioteca. Para mais informações, consulte a documentação oficial: https://filamentphp.com/docs/3.x/.

2. Estilizações Comuns entre os Painéis
Algumas estilizações comuns entre o painel público e o painel do candidato são aplicadas pela função applyFilamentPanelStyles, localizada na pasta App\Helpers, na declaração do painel.

3. Estilização Manual da Barra Superior (Topbar)

Topbar: Estilização feita por meio de um override do componente do Filament, localizado em views/vendor/filament-panels.

Logo da Topbar: Customização do logo na topbar está no arquivo views/vendor/filament-panels/components/topbar/index.blade.php

4. Estilização manual da Sidebar
Ajuste da logo exibida na sidebar em:
views/vendor/filament-panels/components/sidebar/index.blade.php

5. Outros Ajustes: Outras estilizações, como o menu de hambúrguer e o background da barra superior, foram configuradas no arquivo CSS em resources/css/filament.css.

Nota: Após realizar alterações no CSS, é necessário publicar os assets atualizados com o comando:
php artisan filament:assets