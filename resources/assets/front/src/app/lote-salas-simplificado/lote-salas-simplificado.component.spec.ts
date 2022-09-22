import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { LoteSalasSimplificadoComponent } from './lote-salas-simplificado.component';

describe('LoteSalasSimplificadoComponent', () => {
  let component: LoteSalasSimplificadoComponent;
  let fixture: ComponentFixture<LoteSalasSimplificadoComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ LoteSalasSimplificadoComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(LoteSalasSimplificadoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
