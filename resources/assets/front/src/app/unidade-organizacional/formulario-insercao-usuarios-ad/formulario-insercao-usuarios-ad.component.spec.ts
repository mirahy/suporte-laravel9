import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FormularioInsercaoUsuariosAdComponent } from './formulario-insercao-usuarios-ad.component';

describe('FormularioInsercaoUsuariosAdComponent', () => {
  let component: FormularioInsercaoUsuariosAdComponent;
  let fixture: ComponentFixture<FormularioInsercaoUsuariosAdComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FormularioInsercaoUsuariosAdComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FormularioInsercaoUsuariosAdComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
