import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SalasOldComponent } from './salas-old.component';

describe('SalasOldComponent', () => {
  let component: SalasOldComponent;
  let fixture: ComponentFixture<SalasOldComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SalasOldComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SalasOldComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
