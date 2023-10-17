import { Component, OnInit } from "@angular/core";
import { CursosMoodleService } from "../cursos-moodle.service";
import { AbstractComponent } from "../abstract-component";
import { Router } from "@angular/router";
import { forEach } from "@angular/router/src/utils/collection";
import { empty } from "@angular-devkit/schematics";

declare var jQuery: any;

@Component({
  selector: "app-meus-cursos",
  templateUrl: "./meus-cursos.component.html",
  styleUrls: ["./meus-cursos.component.less"],
})
export class MeusCursosComponent extends AbstractComponent implements OnInit {
  moodles = [];
  emptyCursos = false;
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

  removeLoading(){
    jQuery('.loading')
      .remove()
  }


  ngOnInit() {
    this.cursosMoodleService.getMoodlesComCursos(6).then((response) => {
      this.moodles = response ;
      let i = 0;
      this.moodles.forEach(element => {
        
          if(element.cursos.length !== 0){
            i++
          }
      });
      if(!i){
        this.emptyCursos = true
      } 
    });
  }
}
