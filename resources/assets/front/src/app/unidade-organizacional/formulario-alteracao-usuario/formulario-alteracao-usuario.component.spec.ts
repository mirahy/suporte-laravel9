import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FormularioAlteracaoUsuarioComponent } from './formulario-alteracao-usuario.component';

describe('FormularioAlteracaoUsuarioComponent', () => {
  let component: FormularioAlteracaoUsuarioComponent;
  let fixture: ComponentFixture<FormularioAlteracaoUsuarioComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FormularioAlteracaoUsuarioComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FormularioAlteracaoUsuarioComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
