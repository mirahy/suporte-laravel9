import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ServidoresMoodleComponent } from './servidores-moodle.component';

describe('ServidoresMoodleComponent', () => {
  let component: ServidoresMoodleComponent;
  let fixture: ComponentFixture<ServidoresMoodleComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ServidoresMoodleComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ServidoresMoodleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
