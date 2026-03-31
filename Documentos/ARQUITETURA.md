# CRMV/TO — Sistema de Educação Continuada
## Versão Refatorada — Documentação Técnica Completa

---

## 📁 NOVA ESTRUTURA DE PASTAS

```
crmv/
├── includes/                       ← Núcleo do sistema
│   ├── config.php                  ← Constantes e configurações de ambiente
│   ├── Database.php                ← Classe PDO (Singleton)
│   ├── Auth.php                    ← Autenticação, sessão, CSRF
│   ├── helpers.php                 ← Funções utilitárias, formatação, flash
│   ├── bootstrap.php               ← Ponto único de inicialização
│   ├── layout_admin_header.php     ← Cabeçalho do painel admin (sidebar + topbar)
│   ├── layout_admin_footer.php     ← Rodapé do painel admin
│   ├── layout_aluno_header.php     ← Cabeçalho da área do aluno
│   └── layout_aluno_footer.php     ← Rodapé da área do aluno
│
├── admin/                          ← Área administrativa
│   ├── dashboard.php               ← Painel principal
│   ├── cursos/
│   │   ├── lista.php               ← Listagem com filtros (modalidade, status, tipo)
│   │   ├── form.php                ← Cadastro/edição com 4 abas
│   │   ├── excluir.php
│   │   ├── del_aula.php
│   │   └── del_material.php
│   ├── usuarios/
│   │   ├── lista.php               ← Busca por nome/CPF/email
│   │   ├── form.php                ← Cadastro/edição
│   │   └── ver.php                 ← Perfil completo + cursos
│   ├── matriculas/
│   │   ├── lista.php               ← Listagem com filtros + ações rápidas
│   │   └── nova.php                ← Matricular veterinário
│   ├── certificados/
│   │   ├── lista.php               ← Certificados emitidos
│   │   ├── emitir.php              ← Emissão manual
│   │   └── ver.php                 ← Visualização/impressão
│   ├── avaliacoes/
│   │   ├── lista.php
│   │   └── form.php                ← Criação de provas e questões
│   ├── relatorios/
│   │   └── index.php
│   └── configuracoes.php
│
├── aluno/                          ← Área do aluno
│   ├── dashboard.php               ← Cursos matriculados com cards
│   ├── curso.php                   ← Consumo do curso (módulos, aulas, progresso)
│   ├── avaliacao.php               ← Interface de resposta à prova
│   ├── certificados.php            ← Meus certificados
│   ├── certificado-ver.php         ← Visualizar/baixar certificado
│   ├── emitir-certificado.php      ← Emissão pelo aluno
│   └── download-material.php       ← Download seguro de materiais
│
├── assets/
│   ├── css/
│   │   └── sistema.css             ← Design system unificado (admin + aluno)
│   └── js/
│       └── app.js                  ← JS global (se necessário)
│
├── uploads/
│   ├── capas/                      ← Imagens de capa dos cursos
│   ├── materiais/                  ← PDFs e documentos de apoio
│   ├── assinaturas/                ← Assinaturas dos instrutores (certificados)
│   └── certificados/               ← PDFs de certificados gerados
│
├── Documentos/
│   ├── crmv_cursos.sql             ← Banco original
│   └── migration.sql               ← Ajustes e novas tabelas (RODAR ESTE)
│
├── index.php                       ← Redireciona conforme perfil
├── login.php
├── logout.php
└── acesso-negado.php
```

---

## 🗄️ BANCO DE DADOS — O QUE MUDAR

### Tabelas NOVAS (adicionar via migration.sql)

| Tabela | Propósito |
|---|---|
| `tbl_aula_progresso` | Controle granular de aulas concluídas por aluno |
| `tbl_avaliacao_respostas` | Respostas do aluno questão a questão |
| `tbl_avaliacao_tentativas` | Controle de tentativas e nota por tentativa |

### Views NOVAS (criadas no migration.sql)

| View | Propósito |
|---|---|
| `vw_dashboard_totais` | Totais para o dashboard admin |
| `vw_matriculas_completo` | JOIN completo para relatórios |
| `vw_certificados` | Dados completos dos certificados |

### Colunas adicionadas

- `tbl_usuarios`: `ativo`, `senha_hash`
- `tbl_cursos`: `requer_avaliacao`, `avaliacao_com_nota`, `nota_minima`, `tentativas_maximas`, `cert_frente_html`, `cert_verso_html`

---

## 🔄 FLUXOS PRINCIPAIS

### Fluxo 1 — Cadastro de Curso (Admin)
```
1. Admin acessa admin/cursos/form.php
2. Preenche Aba 1 (Informações Gerais):
   - Título, tipo, modalidade, status
   - Categoria, instrutor, carga horária
   - Datas/Local (presencial) OU Link EAD
   - Configurações de avaliação
3. Salva → sistema cria o curso → redireciona para Aba 2
4. Aba 2 (Conteúdo EAD):
   - Cria módulos
   - Adiciona aulas a cada módulo (YouTube / link externo)
   - Faz upload de materiais complementares
5. Aba 3 (Avaliação):
   - Cria prova com questões de múltipla escolha
6. Aba 4 (Certificado):
   - Define conteúdo programático (verso)
   - Define validade do certificado
7. Publica o curso (status = PUBLICADO)
```

### Fluxo 2 — Matrícula (Admin)
```
1. Admin acessa admin/matriculas/nova.php
   OU acessa admin/usuarios/ver.php e clica em "Matricular"
2. Seleciona o veterinário (busca por nome/CPF)
3. Seleciona o curso
4. Confirma → matrícula criada com status=ATIVA
5. Aluno recebe acesso imediato
```

### Fluxo 3 — Consumo do Curso (Aluno EAD)
```
1. Aluno loga → dashboard com cursos em cards
2. Clica em "Acessar Curso"
3. Visualiza módulos e aulas na página do curso
4. Clica em uma aula → abre vídeo/link
5. Clica em "Marcar como concluída"
   → Sistema atualiza tbl_aula_progresso
   → Recalcula progresso_ead
   → Se 100% → status = CONCLUIDA
6. Se o curso tem avaliação → botão "Fazer Prova" aparece
7. Após aprovação → certificado gerado automaticamente
```

### Fluxo 4 — Avaliação
```
1. Aluno acessa aluno/avaliacao.php
2. Sistema carrega questões (com randomização opcional)
3. Aluno responde e envia
4. Sistema corrige automaticamente:
   - Conta acertos / total de pontos
   - Calcula nota percentual
5. Registra em tbl_avaliacao_tentativas
6. Se aprovado:
   - Matrícula → status = CONCLUIDA
   - Certificado gerado automaticamente
7. Se reprovado:
   - Verifica tentativas restantes
   - Permite nova tentativa se disponível
```

### Fluxo 5 — Certificado
```
GERAÇÃO AUTOMÁTICA:
- Curso sem avaliação: gerado ao marcar 100% das aulas
- Curso com avaliação: gerado ao ser aprovado na prova

GERAÇÃO MANUAL (Admin):
- admin/certificados/emitir.php?matricula_id=X
- Admin pode emitir para cursos presenciais

ACESSO PELO ALUNO:
- aluno/certificados.php → lista todos os certificados
- aluno/certificado-ver.php?codigo=XXXX-XXXX → visualiza/imprime/baixa

VERIFICAÇÃO PÚBLICA:
- Qualquer pessoa pode validar em /verificar.php?codigo=XXXX
```

---

## 🎨 DESIGN SYSTEM

### Paleta de Cores
```
Azul escuro:  #0d2137 (sidebar, headers)
Azul médio:   #15385c (elementos secundários)
Azul claro:   #2563ae (links, botões primários)
Verde:        #16a34a (sucesso, EAD, aprovação)
Ouro:         #c9a227 (certificados, destaques, ativo)
Vermelho:     #dc2626 (erros, cancelados)
```

### Componentes disponíveis em sistema.css
- `.card` + `.card-header` + `.card-body` + `.card-footer`
- `.stat-card` + `.stat-grid`
- `.btn` + modificadores: `-primario`, `-verde`, `-ouro`, `-verm`, `-ghost`, `-outline-azul`
- `.badge` + `-verde`, `-azul`, `-ouro`, `-verm`, `-cinza`
- `.tabs-bar` + `.tab-btn`
- `.steps-bar` (wizard step-by-step)
- `.filters-bar`
- `.table-container`
- `.progress-bar-wrap` + `.progress-bar-fill`
- `.flash` (mensagens)
- `.modal-overlay` + `.modal`
- `.accordion` + `.accordion-item`
- `.curso-card` + `.curso-grid`
- `.form-group`, `.form-control`, `.form-row-2`, `.form-row-3`

---

## ⚙️ COMO FAZER O DEPLOY

### Passo 1 — Banco de dados
```bash
# 1. Importe o banco original (se ainda não fez)
mysql -u root crmv_cursos < Documentos/crmv_cursos.sql

# 2. Execute o script de migração
mysql -u root crmv_cursos < Documentos/migration.sql
```

### Passo 2 — Arquivos
```bash
# Substitua a pasta do sistema pelo conteúdo refatorado
# Mantenha a pasta uploads/ com os arquivos existentes
```

### Passo 3 — Configuração
```php
// Edite includes/config.php:
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'crmv_cursos');
define('BASE_URL', '/crmv'); // ou '/' se for a raiz
```

### Passo 4 — Permissões
```bash
chmod 755 uploads/
chmod 755 uploads/capas/
chmod 755 uploads/materiais/
chmod 755 uploads/certificados/
chmod 755 uploads/assinaturas/
```

---

## 📋 CHECKLIST DE ENTREGA

- [x] Nova estrutura de pastas e arquivos
- [x] `includes/config.php` — Configurações centralizadas
- [x] `includes/Database.php` — Classe PDO reutilizável
- [x] `includes/Auth.php` — Autenticação + CSRF + controle de perfil
- [x] `includes/helpers.php` — Formatação, flash, upload, paginação
- [x] `includes/bootstrap.php` — Inicialização única
- [x] `assets/css/sistema.css` — Design system completo e responsivo
- [x] `admin/dashboard.php` — Painel com totais e atividade recente
- [x] `admin/cursos/lista.php` — Listagem com filtros por modalidade
- [x] `admin/cursos/form.php` — Formulário em 4 abas (dados, EAD, avaliação, cert)
- [x] `admin/matriculas/lista.php` — Matrículas com filtros e ações rápidas
- [x] `aluno/dashboard.php` — Cards de cursos com progresso
- [x] `aluno/curso.php` — Player de aulas + marcação de conclusão
- [x] `Documentos/migration.sql` — Script de ajuste do banco
- [x] `Documentos/ARQUITETURA.md` — Esta documentação

---

## 🔐 SEGURANÇA IMPLEMENTADA

1. **CSRF**: Token em todos os formulários POST
2. **Sessão segura**: httponly, samesite=Lax, regeneração no login
3. **Controle de acesso**: `Auth::requireAdmin()` e `Auth::requireAluno()` em cada página
4. **Upload seguro**: Validação de extensão + MIME + tamanho. Nome aleatório no disco
5. **Sanitização**: Função `e()` em toda saída HTML
6. **PDO com prepared statements**: Sem SQL injection possível
7. **Verificação de matrícula**: Aluno só acessa curso se estiver matriculado

---
*CRMV/TO — Sistema de Educação Continuada — Versão Refatorada*
