import { Injectable } from "@angular/core";
import { Http } from "@angular/http";
import { $ } from "protractor";

declare var jQuery: any;

@Injectable()
export class CursosMoodleService {
  constructor(private http: Http) {}

  async getMoodles(idArray = [1, 2]) {
    let idMoodles = { idMoodles: idArray };
    return this.http
      .post("/get-moodles", idMoodles)
      .toPromise()
      .then((response: any) => {
        return response.json();
      });
  }

  async getMoodlesComCursos(paramMeses = 6) {
    let meses = { ultimosMeses: paramMeses };
    return this.http
      .post("/get-meus-cursos", meses)
      .toPromise()
      .then((response: any) => {
        return response.json();
      });
  }

  async goMoodle(idMoodle: string, IdCurso: string) {
    let params = { idMoodles: [idMoodle], idCurso: IdCurso };
    return this.http
      .post("/go-moodle", params)
      .toPromise()
      .then((response: any) => {
        return response;
      });
  }

  functionCollapse(id) {
      if(jQuery('#btnMoodlecollapse' + id).hasClass('collapsed')){
        //Alterar seta para cima somente da guia em aberto
        jQuery('#caretMoodlecollapse' + id).removeClass('bi-caret-down-fill');
        jQuery('#caretMoodlecollapse' + id).addClass('bi-caret-up-fill');
      }else{
        //Alterar seta para baixo  da guia fechada
        jQuery('#caretMoodlecollapse' + id).removeClass('bi-caret-up-fill');
        jQuery('#caretMoodlecollapse' + id).addClass('bi-caret-down-fill');
      }
  }
}
