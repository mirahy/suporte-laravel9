import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FormularioPessoasEstatusLotacaoComponent } from './formulario-pessoas-estatus-lotacao.component';

describe('FormularioPessoasEstatusLotacaoComponent', () => {
  let component: FormularioPessoasEstatusLotacaoComponent;
  let fixture: ComponentFixture<FormularioPessoasEstatusLotacaoComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FormularioPessoasEstatusLotacaoComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FormularioPessoasEstatusLotacaoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
