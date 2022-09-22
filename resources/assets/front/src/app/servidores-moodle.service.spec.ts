import { TestBed } from '@angular/core/testing';

import { ServidoresMoodleService } from './servidores-moodle.service';

describe('ServidoresMoodleService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: ServidoresMoodleService = TestBed.get(ServidoresMoodleService);
    expect(service).toBeTruthy();
  });
});
