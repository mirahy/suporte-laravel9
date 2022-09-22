import { AbstractComponentInterface } from "./abstract-component-interface";
import { PlDisciplinasAcademicos } from "./pl-disciplinas-academicos/pl-disciplinas-academicos";

export interface AbstractPLDAComponentInterface extends AbstractComponentInterface {

    plDisciplinasAcademicosTemp: PlDisciplinasAcademicos;
    plDisciplinasAcademicosTempList: Array<PlDisciplinasAcademicos>;
    modoLista:boolean;
    periodoLetivoSelecionadoId:any;
    cursoSelecionadoId;
    faculdadeSelecionadaId;
    disciplinaSelecionadaNome;
    disciplinaSelecionadaNomes:Array<string>;

}