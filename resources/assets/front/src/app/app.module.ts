import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { HttpModule } from '@angular/http';
import { Routes, RouterModule } from '@angular/router';
import { ScrollingModule } from '@angular/cdk/scrolling';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { SalasComponent } from './salas/salas.component';
import { UsuariosComponent } from './usuarios/usuarios.component';
import { AgendaComponent } from './agenda/agenda.component';
import { PeriodoLetivosComponent } from './periodo-letivos/periodo-letivos.component';
import { FaculdadesComponent } from './faculdades/faculdades.component';
import { CursosComponent } from './cursos/cursos.component';
import { PeriodoLetivosCategoriasComponent } from './periodo-letivos-categorias/periodo-letivos-categorias.component';
import { CriaSalasComponent } from './salas/cria-salas/cria-salas.component';
import { SelectUsuarioComponent } from './select-usuario/select-usuario.component';
import { BuscadoresComponent } from './buscadores/buscadores.component';
import { ArquivoComponent } from './arquivo/arquivo.component';
import { ReservasComponent } from './reservas/reservas.component';
import { RecursoComponent } from './recurso/recurso.component';
import { CalendarioComponent } from './calendario/calendario.component';
import { MacroComponent } from './macro/macro.component';
import { SalasOldComponent } from './salas-old/salas-old.component';
import { PlDisciplinasAcademicosComponent } from './pl-disciplinas-academicos/pl-disciplinas-academicos.component';

import { ButtonModule } from 'primeng/button';
import { AccordionModule } from 'primeng/accordion';
import { PanelModule } from 'primeng/panel';
import { SliderModule } from 'primeng/slider';
import { InputTextModule } from 'primeng/inputtext';
import { InputTextareaModule } from 'primeng/inputtextarea';
import { CheckboxModule } from 'primeng/checkbox';
import { DropdownModule } from 'primeng/dropdown';
import { AutoCompleteModule } from 'primeng/autocomplete';
import { CalendarModule } from 'primeng/calendar';
import { FullCalendarModule } from 'primeng/fullcalendar';
import { ColorPickerModule } from 'primeng/colorpicker';
import { RadioButtonModule } from 'primeng/radiobutton';
import { FileUploadModule } from 'primeng/fileupload';

import { UsuarioService } from './usuario.service';
import { SalasService } from './salas.service';
import { DadosService } from './dados.service';
import { ThemeService } from './theme.service';
import { AgendaService } from './agenda.service';
import { MacroService } from './macro.service';
import { RecursoService } from './recurso.service';
import { ReservasService } from './reservas.service';
import { SalasOldService } from './salas-old.service';
import { PeriodoLetivosService } from './periodo-letivos.service';
import { FaculdadeService } from './faculdade.service';
import { CursosService } from './cursos.service';
import { PeriodoLetivosCategoriasService } from './periodo-letivos-categorias.service';
import { PlDisciplinasAcademicosService } from './pl-disciplinas-academicos.service';
//import { ThemeService } from './theme.service';

import { ZerosPipe } from './zeros.pipe';
import { FormatadorDataPipe } from './formatador-data.pipe';
import { FiltroSalasPipe } from './filtro-salas.pipe';
import { FiltroUsuarioPipe } from './filtro-usuario.pipe';
import { FiltroCursosPipe } from './filtro-cursos.pipe';
import { RedimensionarDirective } from './redimensionar.directive';
import { SuperMacroComponent } from './super-macro/super-macro.component';
import { SuperMacroService } from './super-macro.service';
import { CriaLoteSalasComponent } from './lote-salas/cria-lote-salas/cria-lote-salas.component';
import { LoteSalasComponent } from './lote-salas/lote-salas.component';
import { LoteSalasService } from './lote-salas.service';
import { ServidoresMoodleComponent } from './servidores-moodle/servidores-moodle.component';
import { ServidoresMoodleService } from './servidores-moodle.service';
import { FormularioInsercaoUsuariosMoodleComponent } from './servidores-moodle/formulario-insercao-usuarios-moodle/formulario-insercao-usuarios-moodle.component';
import { ObtemPldaComponent } from './pl-disciplinas-academicos/obtem-plda/obtem-plda.component';
import { MacroSuperMacroService } from './macro-super-macro.service';
import { MacroSuperMacroComponent } from './super-macro/macro-super-macro/macro-super-macro.component';
import { LoteSalasSimplificadoComponent } from './lote-salas-simplificado/lote-salas-simplificado.component';
import { LoteSalasSimplificadoService } from './lote-salas-simplificado.service';
import { SalaSimplificadaComponent } from './sala-simplificada/sala-simplificada.component';
import { SalaSimplificadaService } from './sala-simplificada.service';
import { GrupoLotesSimplificadosService } from './grupo-lotes-simplificados.service';
import { LogsComponent } from './logs/logs.component';
import { LogsService } from './logs.service';
import { UnidadeOrganizacionalComponent } from './unidade-organizacional/unidade-organizacional.component';
import { UnidadeOrganizacionalService } from './unidade-organizacional.service';
import { FormularioInsercaoUsuariosAdComponent } from './unidade-organizacional/formulario-insercao-usuarios-ad/formulario-insercao-usuarios-ad.component';
import { FormularioAlteracaoUsuarioComponent } from './unidade-organizacional/formulario-alteracao-usuario/formulario-alteracao-usuario.component';
import { FormularioPessoasEstatusLotacaoComponent } from './usuarios/formulario-pessoas-estatus-lotacao/formulario-pessoas-estatus-lotacao.component';
import { PessoasEstatusLotacaoService } from './pessoas-estatus-lotacao.service';
import { RolesComponent } from './roles/roles.component';
import { RolesService } from './roles.service';
import { MeusCursosComponent } from './meus-cursos/meus-cursos.component';
import { TdCursosComponent } from './td-cursos/td-cursos.component';


const appRoutes: Routes = [
    { path: 'salas', component: SalasComponent },
    { path: 'salas/create', component: CriaSalasComponent },
    { path: 'salas/create/:periodoLetivoKey/:codigoCurso/:codigoDisciplina/:salaTurma', component: CriaSalasComponent },
    { path: 'salas-old', component: SalasOldComponent },
    { path: 'lote-salas', component: LoteSalasComponent },
    { path: 'lote-salas-simplificados', component: LoteSalasSimplificadoComponent },
    { path: 'periodo-letivos', component: PeriodoLetivosComponent },
    { path: 'periodo-letivos-categorias', component: PeriodoLetivosCategoriasComponent },
    { path: 'pl-disciplinas-academicos', component: PlDisciplinasAcademicosComponent},
    { path: 'faculdades', component: FaculdadesComponent },
    { path: 'usuarios', component: UsuariosComponent },
    { path: 'servidores-moodle', component: ServidoresMoodleComponent },
    { path: 'formulario-insere-usuarios', component: FormularioInsercaoUsuariosMoodleComponent },
    { path: 'agenda', component: AgendaComponent },
    { path: 'reservas', component: ReservasComponent },
    { path: 'recursos', component: RecursoComponent },
    { path: 'calendario', component: CalendarioComponent },
    { path: 'macro', component: MacroComponent },
    { path: 'super-macro', component: SuperMacroComponent },
    { path: 'unidade-organizacional', component: UnidadeOrganizacionalComponent },
    { path: 'formulario-insere-ad', component: FormularioInsercaoUsuariosAdComponent },
    { path: 'formulario-altera-usuario', component: FormularioAlteracaoUsuarioComponent },
    { path: 'formulario-pessoas-estatus-lotacao', component: FormularioPessoasEstatusLotacaoComponent },
    { path: 'logs', component: LogsComponent },
    { path: 'roles', component: RolesComponent },
    { path: 'meus-cursos', component: MeusCursosComponent },
    { path: 'td-cursos', component: TdCursosComponent },
    /*{ path: '',
      redirectTo: '/',
      pathMatch: 'full'
    },*/
    { path: '**', component: UsuariosComponent }
];

@NgModule({
    declarations: [
        AppComponent,
        SalasComponent,
        ZerosPipe,
        FormatadorDataPipe,
        RedimensionarDirective,
        UsuariosComponent,
        FiltroSalasPipe,
        FiltroUsuarioPipe,
        FiltroCursosPipe,
        AgendaComponent,
        CalendarioComponent,
        MacroComponent,
        BuscadoresComponent,
        ArquivoComponent,
        ReservasComponent,
        RecursoComponent,
        SelectUsuarioComponent,
        SalasOldComponent,
        PeriodoLetivosComponent,
        FaculdadesComponent,
        CursosComponent,
        PeriodoLetivosCategoriasComponent,
        CriaSalasComponent,
        PlDisciplinasAcademicosComponent,
        SuperMacroComponent,
        CriaLoteSalasComponent,
        LoteSalasComponent,
        ServidoresMoodleComponent,
        FormularioInsercaoUsuariosMoodleComponent,
        ObtemPldaComponent,
        MacroSuperMacroComponent,
        LoteSalasSimplificadoComponent,
        SalaSimplificadaComponent,
        LogsComponent,
        UnidadeOrganizacionalComponent,
        FormularioInsercaoUsuariosAdComponent,
        FormularioAlteracaoUsuarioComponent,
        FormularioPessoasEstatusLotacaoComponent,
        RolesComponent,
        MeusCursosComponent,
        TdCursosComponent,
    ],
    imports: [
        BrowserModule,
        AppRoutingModule,
        FormsModule,
        HttpModule,
        HttpClientModule,
        RouterModule.forRoot(
            appRoutes,
            //{ enableTracing: true } // <-- debugging purposes only
        ),

        // Prime
        BrowserModule,
        BrowserAnimationsModule,
        ButtonModule,
        InputTextModule,
        InputTextareaModule,
        CheckboxModule,
        AutoCompleteModule,
        //AccordionModule,
        //PanelModule,
        CalendarModule,
        SliderModule,
        DropdownModule,
        ColorPickerModule,
        FullCalendarModule,
        RadioButtonModule,
        FileUploadModule,
        ScrollingModule
    ],
    providers: [
        SalasService,
        SalasOldService,
        DadosService,
        UsuarioService,
        AgendaService,
        ThemeService,
        MacroService,
        RecursoService,
        ReservasService,
        PeriodoLetivosService,
        PeriodoLetivosCategoriasService,
        FaculdadeService,
        CursosService,
        PlDisciplinasAcademicosService,
        SuperMacroService,
        MacroSuperMacroService,
        LoteSalasService,
        ServidoresMoodleService,
        LoteSalasSimplificadoService,
        SalaSimplificadaService,
        GrupoLotesSimplificadosService,
        UnidadeOrganizacionalService,
        PessoasEstatusLotacaoService,
        LogsService,
        RolesService
    ],
    bootstrap: [
        AppComponent
    ]
})
export class AppModule { }
