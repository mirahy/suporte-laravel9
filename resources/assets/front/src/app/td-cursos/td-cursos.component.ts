import { AfterViewChecked, Component, OnChanges, OnInit, SimpleChanges } from '@angular/core';
import { CursosMoodleService } from '../cursos-moodle.service';
import { AbstractComponent } from '../abstract-component';

declare var jQuery: any;

@Component({
  selector: 'app-td-cursos',
  templateUrl: './td-cursos.component.html',
  styleUrls: ['./td-cursos.component.less']
})
export class TdCursosComponent extends AbstractComponent implements OnInit, AfterViewChecked{
  moodles = ''
  countMoodles: Number = 0
  constructor(private cursosMoodleService: CursosMoodleService) 
  { 
    super()
  }
  

  initrow(){
    jQuery('.rowinit')
      .before("<div class=row>")
    jQuery('.rowfinal')
       .after('</div>')
    jQuery('.panel')
       .removeClass('rowinit')
    jQuery('.panel')
       .removeClass('rowfinal')
  }

  removeLoading(){
    jQuery('.loading')
      .remove()
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

      this.cursosMoodleService.getMoodlesComCursos(0).then((response) => {
      this.moodles = response ;
      this.countMoodles = this.moodles.length
    });
  }

  ngAfterViewChecked(): void {
    this.initrow()
  }



}
