import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PeriodoLetivosComponent } from './periodo-letivos.component';

describe('PeriodoLetivosComponent', () => {
  let component: PeriodoLetivosComponent;
  let fixture: ComponentFixture<PeriodoLetivosComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PeriodoLetivosComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PeriodoLetivosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
