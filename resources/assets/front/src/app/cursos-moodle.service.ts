import { Injectable } from "@angular/core";
import { Http } from "@angular/http";

declare var jQuery: any;

@Injectable()
export class CursosMoodleService {

  constructor(private http: Http) {}

  async getMoodles(idArray = [1,2]) {
    let idMoodles = {'idMoodles' : idArray}
    return this.http
      .post("/get-moodles", idMoodles)
      .toPromise()
      .then((response: any) => {
        return response.json();
    
      });
  }

  async getMoodlesComCursos(paramMeses = 6) {
    let meses  = {'ultimosMeses' : paramMeses}
    return this.http
      .post("/get-meus-cursos", meses)
      .toPromise()
      .then((response: any) => {
        return response.json();
      });
  }

  async goMoodle(idMoodle:string, IdCurso:string) {
    let params  = {'idMoodles' : [idMoodle], 'idCurso' : IdCurso }
    return this.http
      .post("/go-moodle", params)
      .toPromise()
      .then((response: any) => {
        return response;
      });
  }

  functionCollapse () {
    jQuery('.collapse')
        .on('shown.bs.collapse', function() {
          jQuery(this)
                .parent()
                .find(".glyphicon-chevron-down")
                .removeClass("glyphicon-chevron-down")
                .addClass("glyphicon-chevron-up");
            })
        .on('hidden.bs.collapse', function() {
          jQuery(this)
                .parent()
                .find(".glyphicon-chevron-up")
                .removeClass("glyphicon-chevron-up")
                .addClass("glyphicon-chevron-down");
            });
        }
}
