import { TestBed } from '@angular/core/testing';

import { CursosMoodleService } from './cursos-moodle.service';

describe('CursosMoodleService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: CursosMoodleService = TestBed.get(CursosMoodleService);
    expect(service).toBeTruthy();
  });
});
