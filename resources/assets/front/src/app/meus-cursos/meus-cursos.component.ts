import { Component, OnInit } from "@angular/core";
import { CursosMoodleService } from "../cursos-moodle.service";
import { AbstractComponent } from "../abstract-component";
import { Router } from "@angular/router";

declare var jQuery: any;

@Component({
  selector: "app-meus-cursos",
  templateUrl: "./meus-cursos.component.html",
  styleUrls: ["./meus-cursos.component.less"],
})
export class MeusCursosComponent extends AbstractComponent implements OnInit {
  moodles = '';
  constructor(private cursosMoodleService: CursosMoodleService, private router: Router) {
    super();
  }

  goMoodle(idMoodle:string, IdCurso:string){
    this.cursosMoodleService.goMoodle(idMoodle, IdCurso).then((response) => {
      console.log(response)
      jQuery('#curso' + IdCurso + idMoodle).attr('href', response.text())
    });
  }

  collapse(id){
    this.cursosMoodleService.functionCollapse(id);
  }


  ngOnInit() {
    this.cursosMoodleService.getMoodlesComCursos(6).then((response) => {
      this.moodles = response ;
    });
  }
}
