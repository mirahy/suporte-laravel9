import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { TdCursosComponent } from './td-cursos.component';

describe('TdCursosComponent', () => {
  let component: TdCursosComponent;
  let fixture: ComponentFixture<TdCursosComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TdCursosComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TdCursosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
