import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ObtemPldaComponent } from './obtem-plda.component';

describe('ObtemPldaComponent', () => {
  let component: ObtemPldaComponent;
  let fixture: ComponentFixture<ObtemPldaComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ObtemPldaComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ObtemPldaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
