import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PeriodoLetivosCategoriasComponent } from './periodo-letivos-categorias.component';

describe('PeriodoLetivosCategoriasComponent', () => {
  let component: PeriodoLetivosCategoriasComponent;
  let fixture: ComponentFixture<PeriodoLetivosCategoriasComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PeriodoLetivosCategoriasComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PeriodoLetivosCategoriasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
