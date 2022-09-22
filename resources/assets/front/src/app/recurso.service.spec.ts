import { TestBed } from '@angular/core/testing';

import { RecursoService } from './recurso.service';

describe('RecursoService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: RecursoService = TestBed.get(RecursoService);
    expect(service).toBeTruthy();
  });
});
