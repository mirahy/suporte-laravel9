import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { CriaLoteSalasComponent } from './cria-lote-salas.component';

describe('CriaLoteSalasComponent', () => {
  let component: CriaLoteSalasComponent;
  let fixture: ComponentFixture<CriaLoteSalasComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ CriaLoteSalasComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CriaLoteSalasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
