import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { LoteSalasComponent } from './lote-salas.component';

describe('LoteSalasComponent', () => {
  let component: LoteSalasComponent;
  let fixture: ComponentFixture<LoteSalasComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ LoteSalasComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(LoteSalasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
