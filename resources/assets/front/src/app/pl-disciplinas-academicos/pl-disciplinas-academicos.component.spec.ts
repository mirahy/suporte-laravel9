import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PlDisciplinasAcademicosComponent } from './pl-disciplinas-academicos.component';

describe('PlDisciplinasAcademicosComponent', () => {
  let component: PlDisciplinasAcademicosComponent;
  let fixture: ComponentFixture<PlDisciplinasAcademicosComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PlDisciplinasAcademicosComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PlDisciplinasAcademicosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
